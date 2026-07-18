<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected $baseUrl;
    protected $apiKey;
    protected $cacheDuration = 3600; 

    /**
     * Make HTTP GET request with caching
     */
    protected function get($endpoint, $params = [], $useCache = true)
    {
        $cacheKey = $this->getCacheKey($endpoint, $params);
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(30)
                ->retry(3, 100)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($useCache && $data) {
                    Cache::put($cacheKey, $data, $this->cacheDuration);
                }
                
                return $data;
            }

            Log::error("API Error: {$this->baseUrl}{$endpoint}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error("API Exception: {$this->baseUrl}{$endpoint}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Make HTTP GET request with Bearer token authentication
     */
    protected function getWithAuth($endpoint, $params = [], $useCache = true)
    {
        $cacheKey = $this->getCacheKey($endpoint, $params);
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey
            ])->timeout(30)
              ->retry(3, 100)
              ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($useCache && $data) {
                    Cache::put($cacheKey, $data, $this->cacheDuration);
                }
                
                return $data;
            }

            Log::error("API Auth Error: {$this->baseUrl}{$endpoint}", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error("API Auth Exception: {$this->baseUrl}{$endpoint}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey($endpoint, $params)
    {
        $key = str_replace('/', '_', $endpoint);
        if (!empty($params)) {
            $key .= '_' . md5(json_encode($params));
        }
        return 'api_' . $key;
    }

    /**
     * Clear cache for specific endpoint
     */
    protected function clearCache($endpoint)
    {
        $key = str_replace('/', '_', $endpoint);
        Cache::forget('api_' . $key);
    }
}