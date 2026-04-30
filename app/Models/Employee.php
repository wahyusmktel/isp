<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_number', 'name', 'jabatan', 'departemen',
        'phone', 'email', 'address', 'join_date', 'status', 'notes',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    public static function generateEmployeeNumber(): string
    {
        $last = static::orderByDesc('id')->value('employee_number');
        $next = $last ? ((int) substr($last, 4)) + 1 : 1;
        return 'EMP-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function getDepartemenLabelAttribute(): string
    {
        return match ($this->departemen) {
            'manajemen'    => 'Manajemen',
            'teknis'       => 'Teknis',
            'noc'          => 'NOC',
            'keuangan'     => 'Keuangan',
            'cs'           => 'Customer Service',
            'administrasi' => 'Administrasi',
            default        => ucfirst($this->departemen),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif'  => 'Aktif',
            'cuti'   => 'Cuti',
            'resign' => 'Resign',
            default  => ucfirst($this->status),
        };
    }

    public function getAvatarColorAttribute(): string
    {
        $colors = [
            'bg-gradient-to-br from-violet-500 to-purple-600',
            'bg-gradient-to-br from-blue-500 to-cyan-500',
            'bg-gradient-to-br from-emerald-500 to-teal-500',
            'bg-gradient-to-br from-orange-500 to-amber-500',
            'bg-gradient-to-br from-rose-500 to-pink-500',
            'bg-gradient-to-br from-indigo-500 to-blue-600',
        ];
        return $colors[$this->id % count($colors)];
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function toJsonData(): array
    {
        return [
            'id'              => $this->id,
            'employee_number' => $this->employee_number,
            'name'            => $this->name,
            'jabatan'         => $this->jabatan,
            'departemen'      => $this->departemen,
            'departemen_label'=> $this->departemen_label,
            'phone'           => $this->phone ?? '',
            'email'           => $this->email ?? '',
            'address'         => $this->address ?? '',
            'join_date'       => $this->join_date?->format('Y-m-d') ?? '',
            'join_date_fmt'   => $this->join_date?->format('d M Y') ?? '—',
            'status'          => $this->status,
            'notes'           => $this->notes ?? '',
        ];
    }
}
