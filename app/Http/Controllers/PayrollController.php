<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\SalaryConfig;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', now()->format('Y-m'));
        [$year, $month] = explode('-', $period);

        $payrolls = Payroll::with('employee')
            ->whereYear('period', $year)
            ->whereMonth('period', $month)
            ->orderBy('status')
            ->get();

        $salaryConfigs = SalaryConfig::orderBy('jabatan')->get();

        $stats = [
            'total'   => $payrolls->count(),
            'pending' => $payrolls->where('status', 'pending')->count(),
            'paid'    => $payrolls->where('status', 'paid')->count(),
            'total_amount'  => $payrolls->sum('net_salary'),
            'paid_amount'   => $payrolls->where('status', 'paid')->sum('net_salary'),
            'pending_amount'=> $payrolls->where('status', 'pending')->sum('net_salary'),
        ];

        return view('payroll.index', compact(
            'payrolls', 'salaryConfigs', 'stats', 'period'
        ));
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate(['period' => 'required|date_format:Y-m']);

        $period    = $request->period . '-01';
        $employees = Employee::where('status', 'aktif')->get();
        $configs   = SalaryConfig::pluck('allowance', 'jabatan')
            ->union(SalaryConfig::pluck('base_salary', 'jabatan'));

        // Rebuild as assoc: jabatan → [base, allowance]
        $configMap = SalaryConfig::all()->keyBy('jabatan');

        $created = 0;
        $skipped = 0;

        foreach ($employees as $emp) {
            $exists = Payroll::where('employee_id', $emp->id)
                ->where('period', $period)
                ->exists();

            if ($exists) { $skipped++; continue; }

            $cfg = $configMap->get($emp->jabatan);

            Payroll::create([
                'employee_id' => $emp->id,
                'period'      => $period,
                'base_salary' => $cfg?->base_salary ?? 0,
                'allowance'   => $cfg?->allowance   ?? 0,
                'deduction'   => 0,
                'status'      => 'pending',
            ]);
            $created++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil membuat {$created} slip gaji. {$skipped} sudah ada.",
        ]);
    }

    public function update(Request $request, Payroll $payroll): JsonResponse
    {
        $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'allowance'   => 'required|numeric|min:0',
            'deduction'   => 'required|numeric|min:0',
            'notes'       => 'nullable|string|max:500',
        ]);

        $payroll->update($request->only('base_salary', 'allowance', 'deduction', 'notes'));

        return response()->json([
            'success' => true,
            'message' => "Slip gaji {$payroll->employee?->name} berhasil diperbarui.",
            'payroll' => $payroll->fresh()->toJsonData(),
        ]);
    }

    public function pay(Payroll $payroll): JsonResponse
    {
        if ($payroll->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Gaji sudah dibayar.']);
        }

        // Create linked expense record
        $expense = Expense::create([
            'description'  => "Gaji {$payroll->employee?->name} - " . $payroll->period?->format('F Y'),
            'category'     => 'gaji',
            'amount'       => $payroll->net_salary,
            'expense_date' => now()->toDateString(),
            'notes'        => "Slip gaji periode {$payroll->period?->format('M Y')} • {$payroll->employee?->jabatan}",
            'created_by'   => auth()->id(),
        ]);

        $payroll->update([
            'status'     => 'paid',
            'paid_at'    => now(),
            'expense_id' => $expense->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Gaji {$payroll->employee?->name} sebesar Rp " . number_format($payroll->net_salary, 0, ',', '.') . " berhasil dibayarkan.",
        ]);
    }

    public function payAll(Request $request): JsonResponse
    {
        $request->validate(['period' => 'required|date_format:Y-m']);
        [$year, $month] = explode('-', $request->period);

        $pending = Payroll::with('employee')
            ->whereYear('period', $year)
            ->whereMonth('period', $month)
            ->where('status', 'pending')
            ->get();

        if ($pending->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada gaji pending di periode ini.']);
        }

        $count = 0;
        foreach ($pending as $payroll) {
            $expense = Expense::create([
                'description'  => "Gaji {$payroll->employee?->name} - " . $payroll->period?->format('F Y'),
                'category'     => 'gaji',
                'amount'       => $payroll->net_salary,
                'expense_date' => now()->toDateString(),
                'notes'        => "Slip gaji periode {$payroll->period?->format('M Y')} • {$payroll->employee?->jabatan}",
                'created_by'   => auth()->id(),
            ]);

            $payroll->update([
                'status'     => 'paid',
                'paid_at'    => now(),
                'expense_id' => $expense->id,
            ]);
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil membayar {$count} slip gaji sekaligus.",
        ]);
    }

    public function printPdf(Payroll $payroll)
    {
        $payroll->load('employee');

        $pdf = Pdf::loadView('payroll.pdf', compact('payroll'));
        $pdf->setPaper('A4', 'portrait');

        $name = $payroll->employee?->name ?? 'Pegawai';
        $period = $payroll->period?->format('Y-m') ?? now()->format('Y-m');

        return $pdf->stream("SlipGaji-{$name}-{$period}.pdf");
    }

    public function destroy(Payroll $payroll): JsonResponse
    {
        $name = $payroll->employee?->name ?? '—';

        // Remove linked expense if exists
        if ($payroll->expense_id) {
            Expense::find($payroll->expense_id)?->delete();
        }

        $payroll->delete();

        return response()->json([
            'success' => true,
            'message' => "Slip gaji {$name} berhasil dihapus.",
        ]);
    }

    // ─── Salary Config ────────────────────────────────────────────────────────

    public function storeConfig(Request $request): JsonResponse
    {
        $request->validate([
            'jabatan'     => 'required|in:CEO,Direktur,Manajer,Supervisor,Admin,Keuangan,Customer Service,NOC Engineer,Teknisi,Lainnya',
            'base_salary' => 'required|numeric|min:0',
            'allowance'   => 'required|numeric|min:0',
            'notes'       => 'nullable|string|max:500',
        ]);

        $config = SalaryConfig::updateOrCreate(
            ['jabatan' => $request->jabatan],
            [
                'base_salary' => $request->base_salary,
                'allowance'   => $request->allowance,
                'notes'       => $request->notes,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Konfigurasi gaji jabatan \"{$config->jabatan}\" berhasil disimpan.",
            'config'  => $config->toJsonData(),
        ]);
    }

    public function destroyConfig(SalaryConfig $salaryConfig): JsonResponse
    {
        $jabatan = $salaryConfig->jabatan;
        $salaryConfig->delete();

        return response()->json([
            'success' => true,
            'message' => "Konfigurasi gaji \"{$jabatan}\" berhasil dihapus.",
        ]);
    }
}
