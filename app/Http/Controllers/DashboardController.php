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
}