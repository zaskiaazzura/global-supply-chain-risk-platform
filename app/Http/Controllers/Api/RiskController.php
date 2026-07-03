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
     * Get all risk scores
     * GET /api/risk
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
     * Get risk score for a country
     * GET /api/risk/{country}
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
     * Calculate risk score for a country
     * GET /api/risk/calculate/{country}
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

        $riskScore = $this->riskService->calculateRiskScore($countryModel);

        return response()->json([
            'success' => true,
            'message' => 'Risk score calculated successfully',
            'data' => $riskScore
        ]);
    }
}