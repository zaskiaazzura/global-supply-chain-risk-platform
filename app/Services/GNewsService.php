<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GNewsService extends BaseService
{
    protected $baseUrl = 'https://gnews.io/api/v4';
    protected $cacheDuration = 600; // 10 minutes

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
            return $this->getDummyNews();
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

        // Fallback to dummy news
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

        // Remove duplicates based on title
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
     * Get dummy news (fallback)
     */
    private function getDummyNews()
    {
        return [
            [
                'title' => 'Global Supply Chain Faces New Challenges in 2024',
                'description' => 'Supply chain disruptions continue to impact global trade as companies adapt to new challenges.',
                'content' => 'The global supply chain is facing unprecedented challenges in 2024...',
                'source' => 'Supply Chain News',
                'author' => 'John Doe',
                'url' => 'https://example.com/news/1',
                'image' => 'https://example.com/images/1.jpg',
                'publishedAt' => now()->toISOString()
            ],
            [
                'title' => 'Port Congestion Eases as Trade Volumes Recover',
                'description' => 'Major ports around the world are seeing reduced congestion as trade volumes stabilize.',
                'content' => 'After months of disruption, major ports are reporting improved conditions...',
                'source' => 'Trade Magazine',
                'author' => 'Jane Smith',
                'url' => 'https://example.com/news/2',
                'image' => 'https://example.com/images/2.jpg',
                'publishedAt' => now()->subHours(2)->toISOString()
            ],
            [
                'title' => 'Digital Transformation in Logistics Industry',
                'description' => 'Technology adoption is reshaping the logistics industry with AI and automation.',
                'content' => 'The logistics industry is undergoing a digital transformation...',
                'source' => 'Tech Daily',
                'author' => 'Alex Johnson',
                'url' => 'https://example.com/news/3',
                'image' => 'https://example.com/images/3.jpg',
                'publishedAt' => now()->subHours(4)->toISOString()
            ]
        ];
    }
}