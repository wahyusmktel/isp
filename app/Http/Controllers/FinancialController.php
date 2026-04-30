<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        // Income: paid invoices for selected period
        $incomeQuery = Invoice::with('customer:id,name')
            ->where('status', 'paid')
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->orderByDesc('paid_at');

        $incomes = $incomeQuery->get();

        // Expenses for selected period
        $expenses = Expense::whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->orderByDesc('expense_date')
            ->get();

        $totalIncome  = $incomes->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $netBalance   = $totalIncome - $totalExpense;

        // Last 6 months chart data
        $chartData = $this->buildChartData();

        return view('financial.index', compact(
            'incomes', 'expenses',
            'totalIncome', 'totalExpense', 'netBalance',
            'year', 'month', 'chartData'
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['created_by'] = auth()->id();

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Pengeluaran \"{$expense->description}\" berhasil ditambahkan.",
            'expense' => $expense->toJsonData(),
        ]);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Pengeluaran \"{$expense->description}\" berhasil diperbarui.",
            'expense' => $expense->toJsonData(),
        ]);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $desc = $expense->description;
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => "Pengeluaran \"{$desc}\" berhasil dihapus.",
        ]);
    }

    private function buildChartData(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->startOfMonth()->subMonths($i);
            $label = $date->format('M Y');

            $income = Invoice::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');

            $expense = Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');

            $data[] = [
                'label'   => $label,
                'income'  => (int) $income,
                'expense' => (int) $expense,
            ];
        }
        return $data;
    }

    private function rules(): array
    {
        return [
            'description'  => 'required|string|max:200',
            'category'     => 'required|in:operasional,pemeliharaan,gaji,peralatan,lainnya',
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string|max:1000',
        ];
    }
}
