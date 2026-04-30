<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'description', 'category', 'amount', 'expense_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'operasional'  => 'Operasional',
            'pemeliharaan' => 'Pemeliharaan',
            'gaji'         => 'Gaji',
            'peralatan'    => 'Peralatan',
            default        => 'Lainnya',
        };
    }

    public function toJsonData(): array
    {
        return [
            'id'             => $this->id,
            'description'    => $this->description,
            'category'       => $this->category,
            'category_label' => $this->category_label,
            'amount'         => $this->amount,
            'expense_date'   => $this->expense_date?->format('Y-m-d') ?? '',
            'expense_date_fmt' => $this->expense_date?->format('d M Y') ?? '—',
            'notes'          => $this->notes ?? '',
        ];
    }
}
