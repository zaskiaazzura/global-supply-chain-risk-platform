<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use App\Models\Port;
use App\Models\Article;
use App\Models\RiskScore;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCountries = Country::count();
        $totalPorts = Port::count();
        $totalArticles = Article::count();
        $totalRiskScores = RiskScore::count();

        // Recent users
        $recentUsers = User::latest()->limit(5)->get();
        
        // Recent ports
        $recentPorts = Port::with('country')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCountries',
            'totalPorts',
            'totalArticles',
            'totalRiskScores',
            'recentUsers',
            'recentPorts'
        ));
    }
}