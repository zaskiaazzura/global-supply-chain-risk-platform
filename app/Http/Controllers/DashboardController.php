<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        $ports = Port::with('country')->get();
        $riskScores = RiskScore::with('country')
            ->latest('calculated_at')
            ->get()
            ->groupBy('country_id')
            ->map(function ($group) {
                return $group->first();
            });

        return view('dashboard', compact('countries', 'ports', 'riskScores'));
    }
    
    public function ports()
    {
        $countries = Country::all();
        $ports = Port::with('country')->get();
        
        return view('ports', compact('countries', 'ports'));
    }
    
    public function comparison()
    {
        $countries = Country::all();
        return view('comparison', compact('countries'));
    }

    public function currency()
    {
        return view('currency');
    }

    /**
     * Simpan negara yang dipilih ke session
     */
    public function setSessionCountry(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string',
            'currency' => 'nullable|string'
        ]);

        session([
            'selected_country_code' => $request->country_code,
            'selected_currency' => $request->currency ?? 'IDR'
        ]);

        return response()->json([
            'success' => true,
            'data' => session()->all()
        ]);
    }
}