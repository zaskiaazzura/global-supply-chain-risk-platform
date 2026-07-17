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
        try {
            $code1 = $request->input('country1');
            $code2 = $request->input('country2');

            if (!$code1 || !$code2) {
                return response()->json([
                    'error' => 'Kode negara tidak ditemukan'
                ], 400);
            }

            $country1 = Country::where('code', $code1)->first();
            $country2 = Country::where('code', $code2)->first();

            if (!$country1 || !$country2) {
                return response()->json([
                    'error' => 'Negara tidak ditemukan'
                ], 404);
            }

            $eco1 = $this->worldBank->getAllIndicators($code1);
            $eco2 = $this->worldBank->getAllIndicators($code2);

            $risk1 = RiskScore::where('country_id', $country1->id)
                ->latest('calculated_at')
                ->first();

            $risk2 = RiskScore::where('country_id', $country2->id)
                ->latest('calculated_at')
                ->first();

            $weather1 = null;
            $weather2 = null;

            if ($country1->latitude && $country1->longitude) {
                $weather1 = $this->openMeteo->getCurrentWeather(
                    $country1->latitude,
                    $country1->longitude
                );
            }

            if ($country2->latitude && $country2->longitude) {
                $weather2 = $this->openMeteo->getCurrentWeather(
                    $country2->latitude,
                    $country2->longitude
                );
            }

            // ✅ PASTIKAN CURRENCY DIKIRIM
            return response()->json([
                'success' => true,
                'data' => [
                    'country1' => [
                        'name' => $country1->name,
                        'code' => $country1->code,
                        'flag' => $country1->flag_url,
                        'currency' => $country1->currency,              // ← WAJIB
                        'currency_symbol' => $country1->currency_symbol, // ← WAJIB
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
                    'currency' => $country2->currency,              // ← WAJIB
                    'currency_symbol' => $country2->currency_symbol, // ← WAJIB
                    'gdp' => $eco2['gdp'] ?? null,
                    'inflation' => $eco2['inflation'] ?? null,
                    'population' => $eco2['population'] ?? null,
                    'risk_score' => $risk2->total_risk_score ?? null,
                    'risk_level' => $risk2->risk_level ?? 'Unknown',
                    'weather' => $weather2['current_weather']['temperature'] ?? null,
                ],
                'comparison' => [
                    'gdp_difference' => ($eco1['gdp'] ?? 0) - ($eco2['gdp'] ?? 0),
                    'inflation_difference' => ($eco1['inflation'] ?? 0) - ($eco2['inflation'] ?? 0),
                    'population_difference' => ($eco1['population'] ?? 0) - ($eco2['population'] ?? 0)
                ]]
            ]);

        } catch (\Exception $e) {
            \Log::error('Comparison error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}