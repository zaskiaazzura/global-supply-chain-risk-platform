<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    use HasFactory;

    protected $table = 'risk_scores';  

    protected $fillable = [
        'country_id',
        'weather_risk',
        'inflation_risk',
        'currency_risk',
        'political_risk',
        'logistics_risk',
        'total_risk_score',
        'risk_level',
        'risk_factors',
        'recommendations',
        'calculated_at'
    ];

    protected $casts = [
        'risk_factors' => 'array',
        'recommendations' => 'array',
        'calculated_at' => 'datetime',
        'weather_risk' => 'decimal:2',
        'inflation_risk' => 'decimal:2',
        'currency_risk' => 'decimal:2',
        'political_risk' => 'decimal:2',
        'logistics_risk' => 'decimal:2',
        'total_risk_score' => 'decimal:2'
    ];

    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}