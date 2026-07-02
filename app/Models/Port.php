<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    use HasFactory;

    // Definisikan kolom yang boleh diisi (mass assignable) sesuai skema tabelmu
    protected $fillable = [
        'country_id',
        'name',
        'code',
        'latitude',
        'longitude'
    ];

    /**
     * Relasi Balik: Setiap pelabuhan dimiliki oleh satu negara
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}