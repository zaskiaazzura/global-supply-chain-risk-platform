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

            // Get economic data
            $eco1 = $this->worldBank->getAllIndicators($code1);
            $eco2 = $this->worldBank->getAllIndicators($code2);

            // Get risk scores
            $risk1 = RiskScore::where('country_id', $country1->id)
                ->latest('calculated_at')
                ->first();

            $risk2 = RiskScore::where('country_id', $country2->id)
                ->latest('calculated_at')
                ->first();

            // Get weather
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
        } catch (\Exception $e) {
            \Log::error('Comparison error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}