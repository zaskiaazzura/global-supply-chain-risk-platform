<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\RiskWeight;
use App\Models\NewsCache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RiskScoreService
{
    protected $openMeteo;
    protected $worldBank;
    protected $sentiment;
    protected $cacheDuration = 3600; 
    protected $exchangeRate;

    public function __construct(
        OpenMeteoService $openMeteo,
        WorldBankService $worldBank,
        SentimentAnalysisService $sentiment,
        ExchangeRateService $exchangeRate
    ) {
        $this->openMeteo = $openMeteo;
        $this->worldBank = $worldBank;
        $this->sentiment = $sentiment;
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * Calculate risk score for a country
     */
    public function calculateRiskScore($country)
    {
        try {
            // Get weights from database
            $weights = RiskWeight::all()->keyBy('risk_factor');

            // Calculate individual risks
            $weatherRisk = $this->calculateWeatherRisk($country);
            $inflationRisk = $this->calculateInflationRisk($country);
            $politicalRisk = $this->calculatePoliticalRisk($country);
            $currencyRisk = $this->calculateCurrencyRisk($country);
            $logisticsRisk = $this->calculateLogisticsRisk($country);

            // Get weights with fallback values
            $weatherWeight = isset($weights['weather']) ? $weights['weather']->weight_percentage : 30;
            $inflationWeight = isset($weights['inflation']) ? $weights['inflation']->weight_percentage : 20;
            $politicalWeight = isset($weights['political_news']) ? $weights['political_news']->weight_percentage : 40;
            $currencyWeight = isset($weights['currency']) ? $weights['currency']->weight_percentage : 10;

            // Calculate total risk
            $totalRisk = (
                ($weatherRisk * $weatherWeight / 100) +
                ($inflationRisk * $inflationWeight / 100) +
                ($politicalRisk * $politicalWeight / 100) +
                ($currencyRisk * $currencyWeight / 100)
            );

            $totalRisk = round($totalRisk, 2);

            // Determine risk level
            $riskLevel = 'Low';
            if ($totalRisk >= 70) {
                $riskLevel = 'Critical';
            } elseif ($totalRisk >= 50) {
                $riskLevel = 'High';
            } elseif ($totalRisk >= 30) {
                $riskLevel = 'Medium';
            }

            // Save to database
            $riskScore = RiskScore::updateOrCreate(
                ['country_id' => $country->id],
                [
                    'weather_risk' => $weatherRisk,
                    'inflation_risk' => $inflationRisk,
                    'political_risk' => $politicalRisk,
                    'currency_risk' => $currencyRisk,
                    'logistics_risk' => $logisticsRisk,
                    'total_risk_score' => $totalRisk,
                    'risk_level' => $riskLevel,
                    'risk_factors' => json_encode([
                        'weather' => $weatherRisk,
                        'inflation' => $inflationRisk,
                        'political' => $politicalRisk,
                        'currency' => $currencyRisk,
                        'logistics' => $logisticsRisk
                    ]),
                    'recommendations' => json_encode($this->getRecommendations($totalRisk, $riskLevel)),
                    'calculated_at' => now()
                ]
            );

            return $riskScore;

        } catch (\Exception $e) {
            Log::error("Risk calculation failed for {$country->code}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate weather risk (0-100) with caching
     */
    private function calculateWeatherRisk($country)
    {
        if (!$country->latitude || !$country->longitude) {
            return 0;
        }

        $cacheKey = "weather_risk_{$country->code}";
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($country) {
            try {
                $stormRisk = $this->openMeteo->getStormRisk($country->latitude, $country->longitude);
                return min($stormRisk['score'] ?? 0, 100);
            } catch (\Exception $e) {
                Log::warning("Weather risk failed for {$country->code}: " . $e->getMessage());
                return 0;
            }
        });
    }

    /**
     * Calculate inflation risk (0-100)
     */
    private function calculateInflationRisk($country)
    {
        $cacheKey = "inflation_risk_{$country->code}";
        
        return Cache::remember($cacheKey, 86400, function () use ($country) {
            try {
                $inflation = $this->worldBank->getInflation($country->code);
                
                if (!$inflation) {
                    return 0;
                }

                // Convert inflation rate to risk score
                if ($inflation > 20) return 100;
                if ($inflation > 10) return 80;
                if ($inflation > 5) return 50;
                if ($inflation > 3) return 30;
                if ($inflation > 1) return 10;
                
                return 0;
            } catch (\Exception $e) {
                Log::warning("Inflation risk failed for {$country->code}: " . $e->getMessage());
                return 0;
            }
        });
    }

    /**
     * Calculate political risk (0-100)
     */
    private function calculatePoliticalRisk($country)
    {
        try {
            $news = NewsCache::where('country_id', $country->id)
                ->latest('published_at')
                ->limit(20)
                ->get();

            if ($news->isEmpty()) {
                return 0;
            }

            $sentiment = $this->sentiment->analyzeNews($news);
            $risk = $sentiment['negative'] ?? 0;
            
            return min($risk, 100);
        } catch (\Exception $e) {
            Log::warning("Political risk failed for {$country->code}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate currency risk (0-100)
     */
    private function calculateCurrencyRisk($country)
    {
        try {
            $currency = \App\Models\Currency::where('code', $country->currency)->first();
            
            if (!$currency) {
                return 0;
            }

            // Ambil historical rates untuk 30 hari terakhir
            $historical = $this->exchangeRate->getHistoricalRates($country->currency, 30);
            
            if (!$historical || count($historical) < 2) {
                return 0;
            }

            // Hitung volatilitas dari historical rates
            $rates = array_column($historical, 'rate');
            $mean = array_sum($rates) / count($rates);
            $variance = 0;
            foreach ($rates as $rate) {
                $variance += pow($rate - $mean, 2);
            }
            $variance /= count($rates);
            $stdDev = sqrt($variance);
            
            // Koefisien variasi (CV) sebagai ukuran risiko
            $cv = $mean > 0 ? ($stdDev / $mean) * 100 : 0;
            
            // Konversi ke skala 0-100
            $risk = min($cv * 2, 100);
            
            return round($risk, 2);
            
        } catch (\Exception $e) {
            \Log::warning("Currency risk failed for {$country->code}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculate logistics risk (0-100)
     */
    private function calculateLogisticsRisk($country)
    {
        try {
            $portCount = \App\Models\Port::where('country_id', $country->id)->count();
            
            if ($portCount === 0) {
                return 50;
            }

            $weatherRisk = $this->calculateWeatherRisk($country);
            $risk = ($weatherRisk * 0.6) + (($portCount > 5) ? 10 : 30);
            
            return min(round($risk, 2), 100);
        } catch (\Exception $e) {
            Log::warning("Logistics risk failed for {$country->code}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get recommendations based on risk level
     */
    private function getRecommendations($score, $level)
    {
        $recommendations = [];

        if ($level === 'Critical' || $score > 70) {
            $recommendations[] = 'Immediate action required - consider alternative suppliers';
            $recommendations[] = 'High risk - diversify supply chain routes';
            $recommendations[] = 'Monitor situation closely - consider insurance options';
        } elseif ($level === 'High' || $score > 50) {
            $recommendations[] = 'Monitor conditions regularly';
            $recommendations[] = 'Have contingency plans ready';
            $recommendations[] = 'Consider hedging currency risks';
        } elseif ($level === 'Medium' || $score > 30) {
            $recommendations[] = 'Regular monitoring recommended';
            $recommendations[] = 'Maintain communication with suppliers';
        } else {
            $recommendations[] = 'Low risk - continue normal operations';
            $recommendations[] = 'Maintain standard monitoring practices';
        }

        return $recommendations;
    }

    /**
     * Get risk summary for all countries
     */
    public function getRiskSummary()
    {
        $countries = Country::all();
        $summary = [];

        foreach ($countries as $country) {
            $latestRisk = $country->riskScores()->latest('calculated_at')->first();
            
            if ($latestRisk) {
                $summary[] = [
                    'country' => $country->name,
                    'code' => $country->code,
                    'risk_score' => $latestRisk->total_risk_score,
                    'risk_level' => $latestRisk->risk_level,
                    'updated_at' => $latestRisk->calculated_at
                ];
            }
        }

        return $summary;
    }
}