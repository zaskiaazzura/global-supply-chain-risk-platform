<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'risk_factor',
        'weight_percentage',
        'description',
        'is_active'
    ];
}