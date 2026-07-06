<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\RiskScore;
use App\Services\WorldBankService;
use App\Services\OpenMeteoService;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    protected $worldBank;
    protected $openMeteo;

    public function __construct(WorldBankService $worldBank, OpenMeteoService $openMeteo)
    {
        $this->worldBank = $worldBank;
        $this->openMeteo = $openMeteo;
    }

    public function index()
    {
        $countries = Country::all();
        return view('comparison', compact('countries'));
    }

    public function compare(Request $request)
    {
        $code1 = $request->input('country1');
        $code2 = $request->input('country2');


        $country1 = Country::where('code', $code1)->first();
        $country2 = Country::where('code', $code2)->first();

        if (!$country1 || !$country2) {
            return response()->json(['error' => 'Country not found'], 404);
        }

        // Get economic data
        $eco1 = $this->worldBank->getAllIndicators($code1);
        $eco2 = $this->worldBank->getAllIndicators($code2);

        // Get risk scores
        $risk1 = RiskScore::where('country_id', $country1->id)->latest('calculated_at')->first();
        $risk2 = RiskScore::where('country_id', $country2->id)->latest('calculated_at')->first();

        // Get weather
        $weather1 = $this->openMeteo->getCurrentWeather($country1->latitude, $country1->longitude);
        $weather2 = $this->openMeteo->getCurrentWeather($country2->latitude, $country2->longitude);

        return response()->json([
            'country1' => [
                'name' => $country1->name,
                'code' => $country1->code,
                'flag' => $country1->flag_url,
                'currency' => $country1->currency,
                'gdp' => $eco1['gdp'] ?? null,
                'inflation' => $eco1['inflation'] ?? null,
                'population' => $eco1['population'] ?? null,
                'risk_score' => $risk1->total_risk_score ?? null,
                'risk_level' => $risk1->risk_level ?? 'Unknown',
                'weather' => $weather1['current_weather']['temperature'] ?? null,
            ],
            'country2' => [
                'name' => $country2->name,
                'code' => $country2->code,
                'flag' => $country2->flag_url,
                'currency' => $country2->currency,
                'gdp' => $eco2['gdp'] ?? null,
                'inflation' => $eco2['inflation'] ?? null,
                'population' => $eco2['population'] ?? null,
                'risk_score' => $risk2->total_risk_score ?? null,
                'risk_level' => $risk2->risk_level ?? 'Unknown',
                'weather' => $weather2['current_weather']['temperature'] ?? null,
            ]
        ]);
    }
}