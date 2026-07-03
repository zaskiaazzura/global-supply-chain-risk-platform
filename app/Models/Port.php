<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'country_id', 'city', 'latitude', 'longitude',
        'type', 'size', 'max_draft', 'annual_throughput', 'timezone',
        'facilities', 'is_active'
    ];

    protected $casts = [
        'facilities' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function originRoutes()
    {
        return $this->hasMany(ShippingRoute::class, 'origin_port_id');
    }

    public function destinationRoutes()
    {
        return $this->hasMany(ShippingRoute::class, 'destination_port_id');
    }
}