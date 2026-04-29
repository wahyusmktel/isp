<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name', 'category', 'speed_download', 'speed_upload',
        'price', 'contention', 'description', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'is_active'      => 'boolean',
        'speed_download' => 'integer',
        'speed_upload'   => 'integer',
        'sort_order'     => 'integer',
    ];

    protected $attributes = [
        'is_active'  => true,
        'sort_order' => 0,
    ];

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'bisnis'    => 'Bisnis',
            'dedicated' => 'Dedicated',
            default     => 'Home',
        };
    }

    public function toJsonData(): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'category'       => $this->category,
            'speed_download' => $this->speed_download,
            'speed_upload'   => $this->speed_upload,
            'price'          => (float) $this->price,
            'formatted_price'=> $this->formatted_price,
            'contention'     => $this->contention,
            'description'    => $this->description,
            'is_active'      => $this->is_active,
            'sort_order'     => $this->sort_order,
        ];
    }
}
