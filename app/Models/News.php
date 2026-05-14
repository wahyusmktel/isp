<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'cover_image', 'author',
        'category', 'status', 'published_at', 'meta_title', 'meta_description', 'view_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function comments()
    {
        return $this->hasMany(NewsComment::class)->where('is_approved', true)->latest();
    }

    public static function generateSlug(string $title, ?int $exceptId = null): string
    {
        $slug = $base = Str::slug($title);
        $i = 1;
        while (
            static::where('slug', $slug)
                  ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
                  ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) return null;
        if (str_starts_with($this->cover_image, 'http')) return $this->cover_image;
        return asset('uploads/news/' . $this->cover_image);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'pengumuman' => 'Pengumuman',
            'gangguan'   => 'Gangguan',
            'promo'      => 'Promo',
            'tips'       => 'Tips & Trik',
            default      => 'Umum',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'pengumuman' => 'blue',
            'gangguan'   => 'red',
            'promo'      => 'amber',
            'tips'       => 'green',
            default      => 'gray',
        };
    }

    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function getSeoDescriptionAttribute(): string
    {
        if ($this->meta_description) return $this->meta_description;
        if ($this->excerpt) return Str::limit(strip_tags($this->excerpt), 155);
        return Str::limit(strip_tags($this->body), 155);
    }

    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 200));
    }
}
