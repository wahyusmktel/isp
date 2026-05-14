<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsComment extends Model
{
    protected $fillable = ['news_id', 'name', 'email', 'comment', 'is_approved'];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
