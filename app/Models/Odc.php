<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Odc extends Model
{
    protected $fillable = [
        'name', 'location', 'latitude', 'longitude', 'capacity', 'notes',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'capacity' => 'integer',
    ];

    public function odps(): HasMany
    {
        return $this->hasMany(Odp::class);
    }

    public function toJsonData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'capacity' => $this->capacity,
            'notes' => $this->notes,
            'odps_count' => $this->odps_count ?? $this->odps()->count(),
        ];
    }
}
