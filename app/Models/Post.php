<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    public function scopeActive($query)
    {
        return $query->where('is_draft', false)
            ->where('published_at', '<=', now());
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_draft', false)
            ->where('publish_at', '>', now());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    use HasFactory;
}
