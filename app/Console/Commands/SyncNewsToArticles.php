<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsCache;
use App\Models\Article;
use App\Services\SentimentAnalysisService;
use Illuminate\Support\Str;

class SyncNewsToArticles extends Command
{
    protected $signature = 'news:sync-to-articles';
    protected $description = 'Sync news from cache to articles table';

    protected $sentimentService;

    public function __construct(SentimentAnalysisService $sentimentService)
    {
        parent::__construct();
        $this->sentimentService = $sentimentService;
    }

    public function handle()
    {
        $this->info('📰 Syncing news to articles...');

        $news = NewsCache::all();
        $this->info('📊 Total news: ' . $news->count());

        $saved = 0;
        $skipped = 0;

        foreach ($news as $item) {
            // Cek apakah sudah ada di articles berdasarkan url
            if ($item->url && Article::where('url', $item->url)->exists()) {
                $skipped++;
                continue;
            }

            // Analisis sentimen
            $text = ($item->title ?? '') . ' ' . ($item->description ?? '');
            $sentiment = $this->sentimentService->analyzeText($text);

            try {
                Article::create([
                    'title' => $item->title ?? 'No Title',
                    'slug' => Str::slug($item->title ?? 'no-title') . '-' . uniqid(),
                    'content' => $item->content ?? $item->description ?? '',
                    'excerpt' => $item->description ?? '',
                    'author' => $item->author ?? $item->source ?? 'Unknown',
                    'source' => $item->source ?? 'Unknown',
                    'url' => $item->url ?? null,
                    'category' => $item->category ?? 'logistics',
                    'country_id' => $item->country_id,
                    'featured_image' => $item->image_url ?? null,
                    'is_published' => true,
                    'published_at' => $item->published_at ?? now(),
                    'positive_score' => $sentiment['positive'],
                    'negative_score' => $sentiment['negative'],
                    'neutral_score' => $sentiment['neutral'],
                    'sentiment' => $sentiment['positive'] > $sentiment['negative'] ? 'positive' : ($sentiment['negative'] > $sentiment['positive'] ? 'negative' : 'neutral'),
                    'sentiment_score' => $sentiment['positive'] - $sentiment['negative'] / ($sentiment['positive'] + $sentiment['negative'] + $sentiment['neutral'] + 1)
                ]);
                $saved++;
            } catch (\Exception $e) {
                $this->error("❌ Failed: " . $e->getMessage());
                $skipped++;
            }
        }

        $this->info("✅ Saved: $saved");
        $this->warn("⏭️ Skipped: $skipped");
    }
}