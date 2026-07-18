<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCache extends Model
{
    use HasFactory;

    protected $table = 'news_cache';  

    protected $fillable = [
        'country_id',
        'title',
        'description',
        'content',
        'source',
        'author',
        'url',
        'image_url',
        'published_at',
        'category',
        'tags',
        'positive_score',
        'negative_score',
        'neutral_score',
        'sentiment',
        'sentiment_score'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
        'positive_score' => 'integer',
        'negative_score' => 'integer',
        'neutral_score' => 'integer',
        'sentiment_score' => 'decimal:2'
    ];

    // RELATIONSHIP
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}