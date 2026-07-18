<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'indicator_type',
        'value',
        'unit',
        'year',
        'quarter',
        'source'
    ];

    protected $casts = [
        'value' => 'decimal:4',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}