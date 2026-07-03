<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use App\Models\RiskWeight;
use App\Models\NewsCache;
use Illuminate\Support\Facades\Log;

class RiskScoreService
{
    protected $openMeteo;
    protected $worldBank;
    protected $sentiment;

    public function __construct(
        OpenMeteoService $openMeteo,
        WorldBankService $worldBank,
        SentimentAnalysisService $sentiment
    ) {
        $this->openMeteo = $openMeteo;
        $this->worldBank = $worldBank;
        $this->sentiment = $sentiment;
    }

    /**
     * Calculate risk score for a country
     */
    public function calculateRiskScore($country)
    {
        // Get weights from database
        $weights = RiskWeight::all()->keyBy('risk_factor');

        // Calculate individual risks
        $weatherRisk = $this->calculateWeatherRisk($country);
        $inflationRisk = $this->calculateInflationRisk($country);
        $politicalRisk = $this->calculatePoliticalRisk($country);
        $currencyRisk = $this->calculateCurrencyRisk($country);
        $logisticsRisk = $this->calculateLogisticsRisk($country);

        // Get weights
        $weatherWeight = $weights['weather']->weight_percentage ?? 30;
        $inflationWeight = $weights['inflation']->weight_percentage ?? 20;
        $politicalWeight = $weights['political_news']->weight_percentage ?? 40;
        $currencyWeight = $weights['currency']->weight_percentage ?? 10;

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
        $riskScore = RiskScore::create([
            'country_id' => $country->id,
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
        ]);

        return $riskScore;
    }

    /**
     * Calculate weather risk (0-100)
     */
    private function calculateWeatherRisk($country)
    {
        if (!$country->latitude || !$country->longitude) {
            return 0;
        }

        $stormRisk = $this->openMeteo->getStormRisk($country->latitude, $country->longitude);
        
        return min($stormRisk['score'] ?? 0, 100);
    }

    /**
     * Calculate inflation risk (0-100)
     */
    private function calculateInflationRisk($country)
    {
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
    }

    /**
     * Calculate political risk (0-100)
     */
    private function calculatePoliticalRisk($country)
    {
        // Get recent news for sentiment analysis
        $news = NewsCache::where('country_id', $country->id)
            ->latest('published_at')
            ->limit(20)
            ->get();

        if ($news->isEmpty()) {
            return 0;
        }

        $sentiment = $this->sentiment->analyzeNews($news);
        
        // Convert negative sentiment to risk
        $risk = $sentiment['negative'] ?? 0;
        
        return min($risk, 100);
    }

    /**
     * Calculate currency risk (0-100)
     */
    private function calculateCurrencyRisk($country)
    {
        // Get currency volatility
        $currency = \App\Models\Currency::where('code', $country->currency)->first();
        
        if (!$currency) {
            return 0;
        }

        $volatility = 0;
        if ($currency->weekly_change) {
            $volatility += abs($currency->weekly_change);
        }
        if ($currency->monthly_change) {
            $volatility += abs($currency->monthly_change);
        }

        // Convert volatility to risk
        $risk = min($volatility * 2, 100);
        
        return round($risk, 2);
    }

    /**
     * Calculate logistics risk (0-100)
     */
    private function calculateLogisticsRisk($country)
    {
        // Check if country has ports
        $portCount = \App\Models\Port::where('country_id', $country->id)->count();
        
        if ($portCount === 0) {
            return 50; // No ports = medium risk
        }

        // Check weather risk for logistics
        $weatherRisk = $this->calculateWeatherRisk($country);
        
        // Logistics risk is combination of weather risk and port availability
        $risk = ($weatherRisk * 0.6) + (($portCount > 5) ? 10 : 30);
        
        return min(round($risk, 2), 100);
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