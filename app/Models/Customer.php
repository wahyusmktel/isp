<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'customer_number', 'name', 'email', 'phone', 'address',
        'package_id', 'ip_address', 'pppoe_user', 'onu_id', 'mac_ont',
        'acs_device_id', 'ont_serial_number', 'wifi_ssid',
        'status', 'is_isolated', 'isolated_at', 'isolation_reason', 'isolation_released_at',
        'join_date', 'billing_date', 'notes',
        'latitude', 'longitude',
    ];

    protected $casts = [
        'join_date' => 'date',
        'is_isolated' => 'boolean',
        'isolated_at' => 'datetime',
        'isolation_released_at' => 'datetime',
        'billing_date' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'suspend' => 'Suspend',
            'terminate' => 'Terminate',
            default => 'Aktif',
        };
    }

    public function toJsonData(): array
    {
        return [
            'id' => $this->id,
            'customer_number' => $this->customer_number ?? '',
            'name' => $this->name,
            'email' => $this->email ?? '',
            'phone' => $this->phone,
            'address' => $this->address,
            'package_id' => $this->package_id,
            'package_name' => $this->package?->name ?? '—',
            'package_cat' => $this->package?->category ?? 'home',
            'ip_address' => $this->ip_address ?? '',
            'pppoe_user' => $this->pppoe_user ?? '',
            'onu_id' => $this->onu_id ?? '',
            'mac_ont' => $this->mac_ont ?? '',
            'acs_device_id' => $this->acs_device_id ?? '',
            'ont_serial_number' => $this->ont_serial_number ?? '',
            'wifi_ssid' => $this->wifi_ssid ?? '',
            'status' => $this->status,
            'is_isolated' => (bool) $this->is_isolated,
            'isolated_at' => $this->isolated_at?->format('Y-m-d H:i:s') ?? '',
            'isolation_reason' => $this->isolation_reason ?? '',
            'join_date' => $this->join_date?->format('Y-m-d') ?? '',
            'join_date_fmt' => $this->join_date?->format('d M Y') ?? '—',
            'billing_date' => $this->billing_date ?? 1,
            'notes' => $this->notes ?? '',
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
