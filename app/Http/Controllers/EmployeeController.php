<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search', '');
        $departemen = $request->get('departemen', '');
        $status     = $request->get('status', '');

        $query = Employee::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($departemen) {
            $query->where('departemen', $departemen);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $employees = $query->orderByRaw("FIELD(status,'aktif','cuti','resign')")
                           ->orderBy('name')
                           ->get();

        $stats = [
            'total'  => Employee::count(),
            'aktif'  => Employee::where('status', 'aktif')->count(),
            'cuti'   => Employee::where('status', 'cuti')->count(),
            'resign' => Employee::where('status', 'resign')->count(),
        ];

        return view('employees.index', compact('employees', 'stats', 'search', 'departemen', 'status'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['employee_number'] = Employee::generateEmployeeNumber();

        $employee = Employee::create($validated);

        return response()->json([
            'success'  => true,
            'message'  => "Pegawai \"{$employee->name}\" berhasil ditambahkan.",
            'employee' => $employee->toJsonData(),
        ]);
    }

    public function update(Request $request, Employee $employee): JsonResponse
    {
        $validated = $request->validate($this->rules($employee->id));
        $employee->update($validated);

        return response()->json([
            'success'  => true,
            'message'  => "Data pegawai \"{$employee->name}\" berhasil diperbarui.",
            'employee' => $employee->toJsonData(),
        ]);
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $name = $employee->name;
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => "Pegawai \"{$name}\" berhasil dihapus.",
        ]);
    }

    public function printIdCard(Employee $employee)
    {
        $avatarColors = ['#7c3aed', '#2563eb', '#0891b2', '#059669', '#ea580c', '#e11d48'];
        $avatarBg = $avatarColors[$employee->id % count($avatarColors)];

        $brandColor = '#0284c7';

        $deptColor = match($employee->departemen) {
            'manajemen'    => '#6d28d9',
            'teknis'       => '#1d4ed8',
            'noc'          => '#0369a1',
            'keuangan'     => '#065f46',
            'cs'           => '#c2410c',
            default        => '#374151',
        };
        $deptBg = match($employee->departemen) {
            'manajemen'    => '#ede9fe',
            'teknis'       => '#dbeafe',
            'noc'          => '#e0f2fe',
            'keuangan'     => '#d1fae5',
            'cs'           => '#ffedd5',
            default        => '#f3f4f6',
        };

        $pdf = Pdf::loadView('employees.idcard', compact(
            'employee', 'avatarBg', 'brandColor', 'deptColor', 'deptBg'
        ));

        // CR80 standard: 54mm × 85.6mm in points (1mm = 2.8346pt)
        $pdf->setPaper([0, 0, 153.07, 242.65]);

        return $pdf->stream("IDCard-{$employee->employee_number}.pdf");
    }

    private function rules($id = null): array
    {
        return [
            'name'       => 'required|string|max:200',
            'jabatan'    => 'required|in:CEO,Direktur,Manajer,Supervisor,Admin,Keuangan,Customer Service,NOC Engineer,Teknisi,Lainnya',
            'departemen' => 'required|in:manajemen,teknis,noc,keuangan,cs,administrasi',
            'phone'      => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:200' . ($id ? '|unique:employees,email,' . $id : '|unique:employees,email'),
            'address'    => 'nullable|string|max:500',
            'join_date'  => 'required|date',
            'status'     => 'required|in:aktif,cuti,resign',
            'notes'      => 'nullable|string|max:1000',
        ];
    }
}
