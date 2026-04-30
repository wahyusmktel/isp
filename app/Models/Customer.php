<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address',
        'package_id', 'ip_address', 'pppoe_user', 'onu_id',
        'status', 'join_date', 'billing_date', 'notes',
    ];

    protected $casts = [
        'join_date'    => 'date',
        'billing_date' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'suspend'   => 'Suspend',
            'terminate' => 'Terminate',
            default     => 'Aktif',
        };
    }

    public function toJsonData(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email ?? '',
            'phone'        => $this->phone,
            'address'      => $this->address,
            'package_id'   => $this->package_id,
            'package_name' => $this->package?->name ?? '—',
            'package_cat'  => $this->package?->category ?? 'home',
            'ip_address'   => $this->ip_address ?? '',
            'pppoe_user'   => $this->pppoe_user ?? '',
            'onu_id'       => $this->onu_id ?? '',
            'status'       => $this->status,
            'join_date'    => $this->join_date?->format('Y-m-d') ?? '',
            'join_date_fmt'=> $this->join_date?->format('d M Y') ?? '—',
            'billing_date' => $this->billing_date ?? 1,
            'notes'        => $this->notes ?? '',
        ];
    }
}
