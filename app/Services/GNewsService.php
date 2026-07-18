<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GNewsService extends BaseService
{
    protected $baseUrl = 'https://gnews.io/api/v4';
    protected $cacheDuration = 600; 

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key');
    }

    /**
     * Search news
     */
    public function searchNews($query, $max = 10, $country = null, $category = null)
    {
        if (!$this->apiKey) {
            Log::warning('GNews API key not configured');
            return [$this->getDummyNews()];
        }

        $endpoint = '/search';
        $params = [
            'q' => $query,
            'apikey' => $this->apiKey,
            'max' => $max,
            'lang' => 'en'
        ];

        if ($country) {
            $params['country'] = $country;
        }

        if ($category) {
            $params['category'] = $category;
        }

        $response = $this->get($endpoint, $params);
        
        if ($response && isset($response['articles'])) {
            return $response['articles'];
        }

        return $this->getDummyNews();
    }

    /**
     * Get logistics and supply chain news
     */
    public function getLogisticsNews($country = null, $max = 10)
    {
        $queries = [
            'supply chain disruption',
            'logistics shipping',
            'trade logistics',
            'port congestion',
            'shipping industry',
            'freight transport'
        ];

        $allNews = [];
        foreach ($queries as $query) {
            $news = $this->searchNews($query, $max / 2, $country);
            if ($news) {
                $allNews = array_merge($allNews, $news);
            }
        }

        $uniqueNews = [];
        $titles = [];
        foreach ($allNews as $article) {
            if (!in_array($article['title'], $titles)) {
                $titles[] = $article['title'];
                $uniqueNews[] = $article;
            }
        }

        return array_slice($uniqueNews, 0, $max);
    }

    /**
     * Get economic news for country
     */
    public function getEconomicNews($country, $max = 10)
    {
        $query = "economy inflation trade logistics supply chain {$country}";
        return $this->searchNews($query, $max, $country, 'business');
    }

    /**
     * Get geopolitical news for country
     */
    public function getGeopoliticalNews($country, $max = 10)
    {
        $query = "politics geopolitical conflict trade war {$country}";
        return $this->searchNews($query, $max, $country, 'world');
    }

    /**
     * Get news by category
     */
    public function getNewsByCategory($category, $max = 10, $country = null)
    {
        $validCategories = ['business', 'world', 'nation', 'technology', 'entertainment', 'sports', 'science', 'health'];
        
        if (!in_array($category, $validCategories)) {
            $category = 'business';
        }

        $params = [
            'category' => $category,
            'apikey' => $this->apiKey,
            'max' => $max
        ];

        if ($country) {
            $params['country'] = $country;
        }

        $endpoint = '/top-headlines';
        $response = $this->get($endpoint, $params);
        
        if ($response && isset($response['articles'])) {
            return $response['articles'];
        }

        return $this->getDummyNews();
    }

    /**
     * Parse news article
     */
    public function parseArticle($article)
    {
        return [
            'title' => $article['title'] ?? null,
            'description' => $article['description'] ?? null,
            'content' => $article['content'] ?? null,
            'source' => $article['source']['name'] ?? $article['source'] ?? 'Unknown Source', 
            'author' => $article['author'] ?? null,
            'url' => $article['url'] ?? null,
            'image_url' => $article['image'] ?? null,
            'published_at' => isset($article['publishedAt']) 
                ? date('Y-m-d H:i:s', strtotime($article['publishedAt']))
                : now()
        ];
    }

    /**
     * Get global logistics news (1 request untuk semua negara)
     */
    public function getGlobalLogisticsNews($max = 150)
    {
        $queries = [
            'supply chain logistics',
            'trade shipping economy',
            'port congestion freight',
            'logistics industry transport'
        ];
        
        $allArticles = [];
        
        foreach ($queries as $query) {
            $news = $this->searchNews($query, $max, null);
            if ($news && count($news) > 0) {
                $allArticles = array_merge($allArticles, $news);
            }
        }
        
        $unique = [];
        $titles = [];
        foreach ($allArticles as $article) {
            if (!in_array($article['title'], $titles)) {
                $titles[] = $article['title'];
                $unique[] = $article;
            }
        }
        
        return $unique;
    }

    /**
     * Get dummy news (fallback when API fails)
     */
    private function getDummyNews()
    {
        // Jika tidak mau dummy, return empty array
        return [
            [
            'title' => 'Supply chain update: Global logistics face new challenges',
            'description' => 'Recent developments in global supply chains...',
            'content' => 'Full content here...',
            'source' => ['name' => 'Supply Chain News'],
            'url' => '#',
            'publishedAt' => now()->toISOString()
            ]
        ];
    }
}