<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'billing_period', 'amount',
        'status', 'due_date', 'paid_at', 'payment_method', 'notes',
    ];

    protected $casts = [
        'billing_period' => 'date',
        'due_date'       => 'date',
        'paid_at'        => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'Lunas',
            'overdue'   => 'Jatuh Tempo',
            'cancelled' => 'Dibatalkan',
            default     => 'Belum Dibayar',
        };
    }

    public function toJsonData(): array
    {
        return [
            'id'             => $this->id,
            'invoice_number' => $this->invoice_number,
            'customer_id'    => $this->customer_id,
            'customer_name'  => $this->customer?->name ?? '—',
            'customer_phone' => $this->customer?->phone ?? '—',
            'billing_period' => $this->billing_period?->format('Y-m') ?? '',
            'billing_period_fmt' => $this->billing_period?->format('F Y') ?? '—',
            'amount'         => $this->amount,
            'status'         => $this->status,
            'due_date'       => $this->due_date?->format('Y-m-d') ?? '',
            'due_date_fmt'   => $this->due_date?->format('d M Y') ?? '—',
            'paid_at'        => $this->paid_at?->format('Y-m-d\TH:i') ?? '',
            'paid_at_fmt'    => $this->paid_at?->format('d M Y, H:i') ?? '—',
            'payment_method' => $this->payment_method ?? '',
            'notes'          => $this->notes ?? '',
        ];
    }
}
