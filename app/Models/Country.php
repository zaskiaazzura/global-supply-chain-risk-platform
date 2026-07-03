<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Port;
use App\Models\WeatherData;
use App\Models\RiskScore;
use App\Models\NewsCache;
use App\Models\Watchlist;
use App\Models\TradeData;
use App\Models\EconomicIndicator;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'alpha2', 'capital', 'currency', 'currency_symbol',
        'region', 'subregion', 'latitude', 'longitude', 'population',
        'gdp', 'inflation_rate', 'unemployment_rate', 'timezone',
        'flag_url', 'languages', 'borders'
    ];

    protected $casts = [
        'languages' => 'array',
        'borders' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'gdp' => 'decimal:2',
        'inflation_rate' => 'decimal:2',
    ];

    // Relationships
    public function ports()
    {
        return $this->hasMany(Port::class);
    }

    public function weatherData()
    {
        return $this->hasMany(WeatherData::class);
    }

    public function riskScores()
    {
        return $this->hasMany(RiskScore::class);
    }

    public function newsCache()
    {
        return $this->hasMany(NewsCache::class);
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function tradeData()
    {
        return $this->hasMany(TradeData::class);
    }

    public function economicIndicators()
    {
        return $this->hasMany(EconomicIndicator::class);
    }

    // Accessors
    public function getFlagEmojiAttribute()
    {
        $regionalOffset = 0x1F1E6;
        $asciiOffset = 65;
        
        $alpha2 = strtoupper($this->alpha2);
        $emoji = '';
        
        for ($i = 0; $i < 2; $i++) {
            $emoji .= mb_chr($regionalOffset + (ord($alpha2[$i]) - $asciiOffset));
        }
        
        return $emoji;
    }

    public function getRiskLevelAttribute()
    {
        $latestRisk = $this->riskScores()->latest('calculated_at')->first();
        return $latestRisk ? $latestRisk->risk_level : 'Unknown';
    }

    public function getCurrentRiskScoreAttribute()
    {
        $latestRisk = $this->riskScores()->latest('calculated_at')->first();
        return $latestRisk ? $latestRisk->total_risk_score : null;
    }
}