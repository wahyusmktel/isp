<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Odp extends Model
{
    protected $fillable = [
        'name', 'router_id', 'odc_id', 'location', 'latitude', 'longitude', 'capacity', 'notes',
    ];

    protected $casts = [
        'capacity'  => 'integer',
        'router_id' => 'integer',
        'odc_id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function odc(): BelongsTo
    {
        return $this->belongsTo(Odc::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function toJsonData(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'router_id'   => $this->router_id,
            'router_name' => $this->router?->name,
            'odc_id'      => $this->odc_id,
            'odc_name'    => $this->odc?->name,
            'location'    => $this->location,
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'capacity'    => $this->capacity,
            'notes'       => $this->notes,
            'customers_count' => $this->customers_count ?? $this->customers()->count(),
        ];
    }
}
