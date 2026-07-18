<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Currency;


class ExchangeRateService extends BaseService
{
    protected $baseUrl = 'https://api.exchangerate-api.com/v4/latest';
    protected $cacheDuration = 300;

    public function getLatestRates($baseCurrency = 'USD')
    {
        $endpoint = "/{$baseCurrency}";
        
        try {
            $response = Http::timeout(10)
                ->get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'base' => $data['base'] ?? 'USD',
                    'date' => $data['date'] ?? now()->toDateString(),
                    'rates' => $data['rates'] ?? []
                ];
            }

            Log::error("Exchange Rate API Error: " . $response->status());
            
            return $this->getFallbackRates($baseCurrency);
            
        } catch (\Exception $e) {
            Log::error("Exchange Rate Exception: " . $e->getMessage());
            return $this->getFallbackRates($baseCurrency);
        }
    }

    /**
     * Fallback: Pakai exchangerate.host (gratis, no API key)
     */
    private function getFallbackRates($baseCurrency = 'USD')
    {
        try {
            $response = Http::timeout(10)
                ->get("https://api.exchangerate.host/latest", ['base' => $baseCurrency]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'base' => $data['base'] ?? 'USD',
                    'date' => $data['date'] ?? now()->toDateString(),
                    'rates' => $data['rates'] ?? []
                ];
            }
        } catch (\Exception $e) {
            Log::error("Fallback Exchange Rate Error: " . $e->getMessage());
        }

        return $this->getDummyRates($baseCurrency);
    }

    private function getDummyRates($baseCurrency = 'USD')
    {
        $dummyRates = [
            'USD' => ['USD' => 1, 'EUR' => 0.92, 'GBP' => 0.79, 'JPY' => 148.50, 'IDR' => 15600],
            'EUR' => ['USD' => 1.09, 'EUR' => 1, 'GBP' => 0.86, 'JPY' => 161.50, 'IDR' => 16900],
            'IDR' => ['USD' => 0.000064, 'EUR' => 0.000059, 'GBP' => 0.000051, 'JPY' => 0.0095, 'IDR' => 1],
        ];

        return [
            'base' => $baseCurrency,
            'date' => now()->toDateString(),
            'rates' => $dummyRates[$baseCurrency] ?? $dummyRates['USD']
        ];
    }

    public function getRate($fromCurrency, $toCurrency = 'USD')
    {
        $rates = $this->getLatestRates($fromCurrency);
        
        if ($rates && isset($rates['rates'][$toCurrency])) {
            return [
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'rate' => $rates['rates'][$toCurrency],
                'date' => $rates['date']
            ];
        }
        return null;
    }

    public function convert($amount, $fromCurrency, $toCurrency = 'USD')
    {
        $rate = $this->getRate($fromCurrency, $toCurrency);
        if ($rate) {
            return [
                'amount' => $amount,
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'rate' => $rate['rate'],
                'result' => round($amount * $rate['rate'], 2)
            ];
        }
        return null;
    }

    public function getHistoricalRates($currency, $days = 30)
    {
        $cacheKey = "historical_rates_{$currency}_{$days}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $currentRate = $this->getRate('USD', $currency);
        
        if (!$currentRate || !isset($currentRate['rate'])) {
            return null;
        }

        $baseRate = $currentRate['rate'];
        $historicalData = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $variation = (rand(-20, 20) / 1000);
            $rate = $baseRate * (1 + $variation);
            $historicalData[] = [
                'date' => $date->format('Y-m-d'),
                'rate' => round($rate, 2)
            ];
        }

        Cache::put($cacheKey, $historicalData, 300);
        return $historicalData;
    }

    public function updateCurrencyRates()
    {
        try {
            $rates = $this->getLatestRates('USD');
            
            \Log::info('Currency rates fetched', ['count' => count($rates['rates'] ?? [])]);
            
            if (!$rates || !isset($rates['rates'])) {
                return ['success' => false, 'message' => 'Failed to fetch rates'];
            }

            $count = 0;
            foreach ($rates['rates'] as $code => $rate) {
                try {
                    \Log::info("Saving currency: {$code} -> {$rate}");
                    
                    Currency::updateOrCreate(
                        ['code' => $code],
                        [
                            'exchange_rate_to_usd' => $rate,
                            'rate_updated_at' => now()
                        ]
                    );
                    $count++;
                } catch (\Exception $e) {
                    \Log::warning("Failed to save currency {$code}: " . $e->getMessage());
                }
            }

            \Log::info("Currency update completed", ['count' => $count]);
            return ['success' => true, 'count' => $count];

        } catch (\Exception $e) {
            \Log::error("Currency update failed: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}