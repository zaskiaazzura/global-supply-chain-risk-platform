<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\WorldBankService;
use App\Services\OpenMeteoService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected $worldBank;
    protected $openMeteo;

    public function __construct(WorldBankService $worldBank, OpenMeteoService $openMeteo)
    {
        $this->worldBank = $worldBank;
        $this->openMeteo = $openMeteo;
    }

    /**
     * GET /api/countries
     * Get all countries with optional filters
     */
    public function index(Request $request)
    {
        $query = Country::query();

        // Filter by region
        if ($request->has('region')) {
            $query->where('region', $request->region);
        }

        // Filter by search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
        }

        // Limit
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $countries = $query->get();

        return response()->json([
            'success' => true,
            'count' => $countries->count(),
            'data' => $countries
        ]);
    }

    /**
     * GET /api/countries/{code}
     * Get detailed country data including economic and weather
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

        // Get economic data from World Bank
        $economicData = $this->worldBank->getAllIndicators($code);

        // Get weather data from Open-Meteo
        $weather = null;
        if ($country->latitude && $country->longitude) {
            $weather = $this->openMeteo->getCurrentWeather(
                $country->latitude,
                $country->longitude
            );
        }

        // Get latest risk score
        $riskScore = $country->riskScores()
            ->latest('calculated_at')
            ->first();

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
     * GET /api/countries/compare/{code1}/{code2}
     * Compare two countries
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
                    'flag' => $country1->flag_url,
                    'gdp' => $eco1['gdp'] ?? null,
                    'inflation' => $eco1['inflation'] ?? null,
                    'population' => $eco1['population'] ?? null,
                    'risk_score' => $risk1->total_risk_score ?? null,
                    'risk_level' => $risk1->risk_level ?? 'Unknown'
                ],
                'country2' => [
                    'name' => $country2->name,
                    'code' => $country2->code,
                    'flag' => $country2->flag_url,
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

    /**
     * GET /api/countries/sync
     * Sync countries from external API (placeholder)
     */
    public function syncFromAPI()
    {
        return response()->json([
            'success' => true,
            'message' => 'Countries already synced via seeder',
            'count' => Country::count()
        ]);
    }
}