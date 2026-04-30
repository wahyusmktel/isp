<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
