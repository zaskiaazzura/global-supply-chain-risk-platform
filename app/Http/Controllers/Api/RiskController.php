<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\RiskScore;
use App\Services\RiskScoreService;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    protected $riskService;

    public function __construct(RiskScoreService $riskService)
    {
        $this->riskService = $riskService;
    }

    /**
     * GET /api/risk
     * Get all risk scores
     */
    public function index()
    {
        $riskScores = RiskScore::with('country')
            ->latest('calculated_at')
            ->get()
            ->groupBy('country_id')
            ->map(function ($group) {
                return $group->first();
            })
            ->values();

        return response()->json([
            'success' => true,
            'count' => $riskScores->count(),
            'data' => $riskScores
        ]);
    }

    /**
     * GET /api/risk/{country}
     * Get risk score for a specific country
     */
    public function show($country)
    {
        $countryModel = Country::where('code', $country)
            ->orWhere('alpha2', $country)
            ->first();

        if (!$countryModel) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        $riskScore = $countryModel->riskScores()
            ->latest('calculated_at')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'country' => $countryModel->name,
                'code' => $countryModel->code,
                'risk' => $riskScore
            ]
        ]);
    }

    /**
     * GET /api/risk/calculate/{country}
     * Calculate risk score for a single country
     */
    public function calculate($country)
    {
        $countryModel = Country::where('code', $country)
            ->orWhere('alpha2', $country)
            ->first();

        if (!$countryModel) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        try {
            $riskScore = $this->riskService->calculateRiskScore($countryModel);

            return response()->json([
                'success' => true,
                'message' => "Risk score calculated successfully for {$countryModel->name}",
                'data' => $riskScore
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate risk score: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/risk/recalculate-all
     * Recalculate risk for all countries (with chunking)
     */
    public function recalculateAll()
    {
        // Set timeout lebih lama
        set_time_limit(600); // 10 menit
        
        $countries = Country::all();
        $total = $countries->count();
        $success = 0;
        $errors = [];

        // Proses per negara dengan delay kecil
        foreach ($countries as $index => $country) {
            try {
                $this->riskService->calculateRiskScore($country);
                $success++;
                
                // Log progress setiap 10 negara
                if ($success % 10 == 0) {
                    \Log::info("Risk calculation progress: {$success}/{$total}");
                }
                
                // Delay 0.5 detik agar tidak overload API
                usleep(500000);
                
            } catch (\Exception $e) {
                $errors[] = $country->name . ': ' . $e->getMessage();
                \Log::warning("Risk calculation failed for {$country->code}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Risk scores recalculated for {$success} of {$total} countries",
            'success_count' => $success,
            'total_count' => $total,
            'errors' => $errors
        ]);
    }
}