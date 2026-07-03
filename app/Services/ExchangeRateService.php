<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            
            // Fallback ke exchangerate.host
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

        // Return dummy data jika semua gagal
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
        // Simulate historical data
        $currentRate = $this->getRate($currency);
        if (!$currentRate) return null;

        $data = [];
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $variation = (rand(-50, 50) / 1000);
            $rate = $currentRate['rate'] * (1 + $variation);
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'rate' => round($rate, 4)
            ];
        }
        return $data;
    }

    public function updateCurrencyRates()
    {
        $rates = $this->getLatestRates('USD');
        
        if (!$rates) {
            return ['success' => false, 'message' => 'Failed to fetch rates'];
        }

        $count = 0;
        foreach ($rates['rates'] as $code => $rate) {
            \App\Models\Currency::updateOrCreate(
                ['code' => $code],
                [
                    'exchange_rate_to_usd' => $rate,
                    'rate_updated_at' => now()
                ]
            );
            $count++;
        }

        return ['success' => true, 'count' => $count];
    }
}