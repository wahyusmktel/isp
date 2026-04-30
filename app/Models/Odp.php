<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Odp extends Model
{
    protected $fillable = [
        'name', 'router_id', 'location', 'capacity', 'notes',
    ];

    protected $casts = [
        'capacity'  => 'integer',
        'router_id' => 'integer',
    ];

    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }

    public function toJsonData(): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'router_id'   => $this->router_id,
            'router_name' => $this->router?->name,
            'location'    => $this->location,
            'capacity'    => $this->capacity,
            'notes'       => $this->notes,
        ];
    }
}
