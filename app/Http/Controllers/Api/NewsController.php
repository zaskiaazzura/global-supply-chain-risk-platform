<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCache;
use App\Models\Country;
use App\Services\GNewsService;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $gNews;
    protected $sentiment;

    public function __construct(GNewsService $gNews, SentimentAnalysisService $sentiment)
    {
        $this->gNews = $gNews;
        $this->sentiment = $sentiment;
    }

    /**
     * Get news
     * GET /api/news
     */
    public function index(Request $request)
    {
        $query = $request->get('q', 'logistics supply chain');
        $max = $request->get('max', 10);
        $country = $request->get('country');

        // Try to get from cache first
        $news = NewsCache::where('category', 'logistics')
            ->when($country, function ($q) use ($country) {
                return $q->whereHas('country', function ($sub) use ($country) {
                    $sub->where('code', $country)->orWhere('alpha2', $country);
                });
            })
            ->latest('published_at')
            ->limit($max)
            ->get();

        // If cache is empty, fetch from API
        if ($news->isEmpty()) {
            $newsData = $this->gNews->getLogisticsNews($country, $max);
            
            if ($newsData) {
                foreach ($newsData as $article) {
                    // Save to cache
                    $countryModel = $country ? Country::where('code', $country)->first() : null;
                    NewsCache::create([
                        'country_id' => $countryModel->id ?? null,
                        'title' => $article['title'] ?? null,
                        'description' => $article['description'] ?? null,
                        'content' => $article['content'] ?? null,
                        'source' => $article['source']['name'] ?? null,
                        'url' => $article['url'] ?? null,
                        'image_url' => $article['image'] ?? null,
                        'published_at' => isset($article['publishedAt']) ? date('Y-m-d H:i:s', strtotime($article['publishedAt'])) : now(),
                        'category' => 'logistics'
                    ]);
                }
                
                $news = collect($newsData);
            }
        }

        return response()->json([
            'success' => true,
            'count' => $news->count(),
            'data' => $news
        ]);
    }

    /**
     * Get news by category
     * GET /api/news/category/{category}
     */
    public function byCategory($category, Request $request)
    {
        $max = $request->get('max', 10);
        $country = $request->get('country');

        $newsData = $this->gNews->getNewsByCategory($category, $max, $country);

        return response()->json([
            'success' => true,
            'category' => $category,
            'count' => count($newsData),
            'data' => $newsData
        ]);
    }

    /**
     * Get news by country
     * GET /api/news/country/{country}
     */
    public function byCountry($country, Request $request)
    {
        $max = $request->get('max', 10);

        $countryModel = Country::where('code', $country)
            ->orWhere('alpha2', $country)
            ->first();

        if (!$countryModel) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        $news = NewsCache::where('country_id', $countryModel->id)
            ->latest('published_at')
            ->limit($max)
            ->get();

        if ($news->isEmpty()) {
            // Fetch from API
            $newsData = $this->gNews->getEconomicNews($country, $max);
            $news = collect($newsData);
        }

        return response()->json([
            'success' => true,
            'country' => $countryModel->name,
            'count' => $news->count(),
            'data' => $news
        ]);
    }

    /**
     * Sentiment analysis for country news
     * GET /api/news/sentiment/{country}
     */
    public function sentimentAnalysis($country)
    {
        $countryModel = Country::where('code', $country)
            ->orWhere('alpha2', $country)
            ->first();

        if (!$countryModel) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        $news = NewsCache::where('country_id', $countryModel->id)
            ->latest('published_at')
            ->limit(10)
            ->get();

        $result = $this->sentiment->analyzeNews($news);

        return response()->json([
            'success' => true,
            'country' => $countryModel->name,
            'data' => $result
        ]);
    }
}