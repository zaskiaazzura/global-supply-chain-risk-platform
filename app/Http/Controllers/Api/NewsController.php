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
     * GET /api/news
     * Get news with filters
     */
    public function index(Request $request)
    {
        $query = $request->input('q', 'logistics supply chain');
        $max = $request->input('max', 10);
        $country = $request->input('country');

        // Try from cache first
        $news = NewsCache::where('category', 'logistics')
            ->when($country, function ($q) use ($country) {
                return $q->whereHas('country', function ($sub) use ($country) {
                    $sub->where('code', $country)->orWhere('alpha2', $country);
                });
            })
            ->latest('published_at')
            ->limit($max)
            ->get();

        // If cache empty, fetch from API
        if ($news->isEmpty()) {
            $newsData = $this->gNews->getLogisticsNews($country, $max);
            
            if ($newsData && count($newsData) > 0) {
                // Cari country model jika ada parameter country
                $countryModel = null;
                if ($country) {
                    $countryModel = Country::where('code', $country)
                        ->orWhere('alpha2', $country)
                        ->first();
                }

                foreach ($newsData as $article) {
                    // HANYA INSERT JIKA ADA countryModel
                    if ($countryModel) {
                        try {
                            NewsCache::create([
                                'country_id' => $countryModel->id,
                                'title' => $article['title'] ?? null,
                                'description' => $article['description'] ?? null,
                                'content' => $article['content'] ?? null,
                                'source' => $article['source']['name'] ?? null,
                                'url' => $article['url'] ?? null,
                                'image_url' => $article['image'] ?? null,
                                'published_at' => isset($article['publishedAt']) 
                                    ? date('Y-m-d H:i:s', strtotime($article['publishedAt']))
                                    : now(),
                                'category' => 'logistics'
                            ]);
                        } catch (\Exception $e) {
                            // Skip jika error (misal duplicate URL)
                        }
                    }
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
     * GET /api/news/category/{category}
     */
    public function byCategory($category, Request $request)
    {
        // ✅ FIX: get() → input()
        $max = $request->input('max', 10);
        $country = $request->input('country');

        $newsData = $this->gNews->getNewsByCategory($category, $max, $country);

        return response()->json([
            'success' => true,
            'category' => $category,
            'count' => count($newsData),
            'data' => $newsData
        ]);
    }

    /**
     * GET /api/news/country/{country}
     */
    public function byCountry($country, Request $request)
    {
        // ✅ FIX: get() → input()
        $max = $request->input('max', 10);

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

        // Jika tidak ada di cache, fetch dari API dan simpan
        if ($news->isEmpty()) {
            $newsData = $this->gNews->getEconomicNews($country, $max);
            
            if ($newsData && count($newsData) > 0) {
                foreach ($newsData as $article) {
                    try {
                        NewsCache::create([
                            'country_id' => $countryModel->id,
                            'title' => $article['title'] ?? null,
                            'description' => $article['description'] ?? null,
                            'content' => $article['content'] ?? null,
                            'source' => $article['source']['name'] ?? null,
                            'url' => $article['url'] ?? null,
                            'image_url' => $article['image'] ?? null,
                            'published_at' => isset($article['publishedAt']) 
                                ? date('Y-m-d H:i:s', strtotime($article['publishedAt']))
                                : now(),
                            'category' => 'economic'
                        ]);
                    } catch (\Exception $e) {
                        // Skip jika error
                    }
                }
                $news = collect($newsData);
            }
        }

        return response()->json([
            'success' => true,
            'country' => $countryModel->name,
            'count' => $news->count(),
            'data' => $news
        ]);
    }

    /**
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