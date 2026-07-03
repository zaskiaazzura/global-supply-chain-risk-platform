<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\RestCountriesService;
use App\Services\WorldBankService;
use App\Services\OpenMeteoService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected $restCountries;
    protected $worldBank;
    protected $openMeteo;

    public function __construct(
        RestCountriesService $restCountries,
        WorldBankService $worldBank,
        OpenMeteoService $openMeteo
    ) {
        $this->restCountries = $restCountries;
        $this->worldBank = $worldBank;
        $this->openMeteo = $openMeteo;
    }

    /**
     * Get all countries
     * GET /api/countries
     */
    public function index()
    {
        $countries = Country::all();
        
        return response()->json([
            'success' => true,
            'count' => $countries->count(),
            'data' => $countries
        ]);
    }

    /**
     * Get country by code
     * GET /api/countries/{code}
     */
    public function show($code)
    {
        $country = Country::where('code', $code)
            ->orWhere('alpha2', $code)
            ->first();
        
        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        // Get additional data from external APIs
        $economicData = $this->worldBank->getAllIndicators($code);
        $weather = $this->openMeteo->getCurrentWeather(
            $country->latitude ?? 0,
            $country->longitude ?? 0
        );
        $riskScore = $country->riskScores()->latest('calculated_at')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'country' => $country,
                'economic' => $economicData,
                'weather' => $weather,
                'risk' => $riskScore
            ]
        ]);
    }

    /**
     * Sync countries from REST Countries API
     * GET /api/countries/sync
     */
    public function syncFromAPI()
    {
        $result = $this->restCountries->syncCountriesToDatabase();
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['success'] ? 'Countries synced successfully' : 'Failed to sync countries',
            'count' => $result['count'] ?? 0
        ]);
    }

    /**
     * Compare two countries
     * GET /api/countries/compare/{code1}/{code2}
     */
    public function compare($code1, $code2)
    {
        $country1 = Country::where('code', $code1)->first();
        $country2 = Country::where('code', $code2)->first();

        if (!$country1 || !$country2) {
            return response()->json([
                'success' => false,
                'message' => 'One or both countries not found'
            ], 404);
        }

        // Get economic data
        $eco1 = $this->worldBank->getAllIndicators($code1);
        $eco2 = $this->worldBank->getAllIndicators($code2);

        // Get risk scores
        $risk1 = $country1->riskScores()->latest('calculated_at')->first();
        $risk2 = $country2->riskScores()->latest('calculated_at')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'country1' => [
                    'name' => $country1->name,
                    'code' => $country1->code,
                    'gdp' => $eco1['gdp'] ?? null,
                    'inflation' => $eco1['inflation'] ?? null,
                    'population' => $eco1['population'] ?? null,
                    'risk_score' => $risk1->total_risk_score ?? null,
                    'risk_level' => $risk1->risk_level ?? 'Unknown'
                ],
                'country2' => [
                    'name' => $country2->name,
                    'code' => $country2->code,
                    'gdp' => $eco2['gdp'] ?? null,
                    'inflation' => $eco2['inflation'] ?? null,
                    'population' => $eco2['population'] ?? null,
                    'risk_score' => $risk2->total_risk_score ?? null,
                    'risk_level' => $risk2->risk_level ?? 'Unknown'
                ],
                'comparison' => [
                    'gdp_difference' => ($eco1['gdp'] ?? 0) - ($eco2['gdp'] ?? 0),
                    'inflation_difference' => ($eco1['inflation'] ?? 0) - ($eco2['inflation'] ?? 0),
                    'population_difference' => ($eco1['population'] ?? 0) - ($eco2['population'] ?? 0)
                ]
            ]
        ]);
    }
}