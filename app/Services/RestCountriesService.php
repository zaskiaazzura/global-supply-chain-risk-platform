<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Log;

class RestCountriesService
{
    /**
     * Get country by code from database
     */
    public function getCountryByCode($code)
    {
        $country = Country::where('code', $code)
            ->orWhere('alpha2', $code)
            ->first();

        if ($country) {
            return [$country->toArray()];
        }

        Log::warning("Country not found: {$code}");
        return null;
    }

    /**
     * Get all countries from database
     */
    public function getAllCountries()
    {
        return Country::all()->toArray();
    }

    /**
     * Get countries by region from database
     */
    public function getCountriesByRegion($region)
    {
        return Country::where('region', $region)->get()->toArray();
    }

    /**
     * Search countries by name from database
     */
    public function searchCountries($name)
    {
        return Country::where('name', 'LIKE', "%{$name}%")->get()->toArray();
    }

    /**
     * Parse country data (no-op, data already in database)
     */
    public function parseCountryData($apiData)
    {
        return $apiData;
    }

    /**
     * Sync countries (already in database)
     */
    public function syncCountriesToDatabase()
    {
        return [
            'success' => true,
            'message' => 'Countries already in database',
            'count' => Country::count()
        ];
    }
}