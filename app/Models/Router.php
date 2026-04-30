<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Router extends Model
{
    protected $fillable = [
        'name', 'host', 'api_port', 'winbox_port', 'username', 'password',
        'location', 'status', 'model', 'firmware', 'pppoe_online',
        'last_check_at', 'notes',
    ];

    protected $casts = [
        'last_check_at' => 'datetime',
        'api_port'      => 'integer',
        'winbox_port'   => 'integer',
        'pppoe_online'  => 'integer',
    ];

    public function odps(): HasMany
    {
        return $this->hasMany(Odp::class);
    }

    public function toJsonData(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'host'          => $this->host,
            'api_port'      => $this->api_port,
            'winbox_port'   => $this->winbox_port,
            'username'      => $this->username,
            'location'      => $this->location,
            'status'        => $this->status,
            'model'         => $this->model,
            'firmware'      => $this->firmware,
            'pppoe_online'  => $this->pppoe_online,
            'last_check_at' => $this->last_check_at?->diffForHumans(),
            'notes'         => $this->notes,
            'odp_count'     => $this->odps_count ?? $this->odps()->count(),
        ];
    }
}
