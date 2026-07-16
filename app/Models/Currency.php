<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate_to_usd',
        'exchange_rate_to_eur',
        'rate_updated_at',
        'daily_change',
        'weekly_change',
        'monthly_change'
    ];

    protected $casts = [
        'rate_updated_at' => 'datetime',
        'exchange_rate_to_usd' => 'decimal:6',
        'exchange_rate_to_eur' => 'decimal:6',
        'daily_change' => 'decimal:4',
        'weekly_change' => 'decimal:4',
        'monthly_change' => 'decimal:4'
    ];
}