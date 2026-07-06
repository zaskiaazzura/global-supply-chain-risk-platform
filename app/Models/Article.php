<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'author',
        'category', 'tags', 'country_id', 'featured_image',
        'view_count', 'is_published', 'published_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}