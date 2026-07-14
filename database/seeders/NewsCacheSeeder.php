<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NewsCache;
use App\Models\Country;
use App\Services\GNewsService;

class NewsCacheSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🌍 Fetching global news...');
        
        $gnews = app(GNewsService::class);
        
        // Hanya 1-5 request untuk semua berita
        $articles = $gnews->getGlobalLogisticsNews(50);
        
        if (!$articles || count($articles) === 0) {
            $this->command->error('❌ No articles found!');
            return;
        }
        
        $this->command->info('📰 Found ' . count($articles) . ' articles');
        
        $saved = 0;
        $countries = Country::all()->keyBy('code');
        
        foreach ($articles as $article) {
            // Coba cari negara dari source country, atau random
            $countryCode = $article['source']['country'] ?? null;
            $country = $countryCode ? ($countries[strtoupper($countryCode)] ?? null) : null;
            
            // Jika tidak ada country, coba assign ke random country
            if (!$country) {
                $country = $countries->random();
            }
            
            if ($country) {
                try {
                    NewsCache::updateOrCreate(
                        ['url' => $article['url'] ?? 'https://example.com/' . uniqid()],
                        [
                            'country_id' => $country->id,
                            'title' => $article['title'] ?? null,
                            'description' => $article['description'] ?? null,
                            'content' => $article['content'] ?? null,
                            'source' => $article['source']['name'] ?? $article['source'] ?? 'Unknown',
                            'author' => $article['author'] ?? null,
                            'image_url' => $article['image'] ?? null,
                            'published_at' => isset($article['publishedAt']) 
                                ? date('Y-m-d H:i:s', strtotime($article['publishedAt'])) 
                                : now(),
                            'category' => 'logistics'
                        ]
                    );
                    $saved++;
                } catch (\Exception $e) {
                    // Skip jika error
                }
            }
        }
        
        $this->command->info("✅ Saved {$saved} articles to database!");
    }
}