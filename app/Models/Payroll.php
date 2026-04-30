<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id', 'period', 'base_salary', 'allowance',
        'deduction', 'status', 'paid_at', 'expense_id', 'notes',
    ];

    protected $casts = [
        'period'  => 'date',
        'paid_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function getNetSalaryAttribute(): int
    {
        return (int) ($this->base_salary + $this->allowance - $this->deduction);
    }

    public function toJsonData(): array
    {
        return [
            'id'           => $this->id,
            'employee_id'  => $this->employee_id,
            'name'         => $this->employee?->name ?? '—',
            'jabatan'      => $this->employee?->jabatan ?? '—',
            'departemen'   => $this->employee?->departemen_label ?? '—',
            'period'       => $this->period?->format('Y-m') ?? '',
            'base_salary'  => $this->base_salary,
            'allowance'    => $this->allowance,
            'deduction'    => $this->deduction,
            'net_salary'   => $this->net_salary,
            'status'       => $this->status,
            'paid_at_fmt'  => $this->paid_at?->format('d M Y') ?? '—',
            'notes'        => $this->notes ?? '',
        ];
    }
}
