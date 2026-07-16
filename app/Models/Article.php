<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'author', 'source', 'url',
        'category', 'tags', 'country_id', 'featured_image',
        'view_count', 'is_published', 'published_at',
        'positive_score', 'negative_score', 'neutral_score', 'sentiment', 'sentiment_score'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'positive_score' => 'integer',
        'negative_score' => 'integer',
        'neutral_score' => 'integer',
        'sentiment_score' => 'decimal:2'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Helper untuk sentiment badge
    public function getSentimentBadgeAttribute()
    {
        if (!$this->sentiment) return 'secondary';
        return match($this->sentiment) {
            'positive' => 'success',
            'negative' => 'danger',
            default => 'warning'
        };
    }

    // Helper untuk format sentiment
    public function getSentimentLabelAttribute()
    {
        return $this->sentiment ? ucfirst($this->sentiment) : 'N/A';
    }
}