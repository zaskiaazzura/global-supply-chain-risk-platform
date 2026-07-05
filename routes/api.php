<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CurrencyController;

// ==================== 1. COUNTRIES ====================
Route::prefix('countries')->group(function () {
    Route::get('/', [CountryController::class, 'index']);
    Route::get('/{code}', [CountryController::class, 'show']);
    Route::get('/compare/{code1}/{code2}', [CountryController::class, 'compare']);
    Route::get('/sync', [CountryController::class, 'syncFromAPI']);
});

// ==================== 2. RISK ====================
Route::prefix('risk')->group(function () {
    Route::get('/', [RiskController::class, 'index']);
    Route::get('/{country}', [RiskController::class, 'show']);
    Route::get('/calculate/{country}', [RiskController::class, 'calculate']);
});

// ==================== 3. PORTS ====================
Route::prefix('ports')->group(function () {
    Route::get('/', [PortController::class, 'index']);
    Route::get('/search', [PortController::class, 'search']);
    Route::get('/country/{countryCode}', [PortController::class, 'byCountry']);
    Route::get('/{id}', [PortController::class, 'show']);
    Route::post('/sync', [PortController::class, 'sync']);
});

// ==================== 4. NEWS ====================
Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/category/{category}', [NewsController::class, 'byCategory']);
    Route::get('/country/{country}', [NewsController::class, 'byCountry']);
    Route::get('/sentiment/{country}', [NewsController::class, 'sentimentAnalysis']);
});

// ==================== 5. CURRENCY ====================
Route::prefix('currency')->group(function () {
    Route::get('/', [CurrencyController::class, 'index']);
    Route::get('/convert', [CurrencyController::class, 'convert']);
    Route::get('/historical/{code}', [CurrencyController::class, 'historical']);
    Route::get('/{code}', [CurrencyController::class, 'show']);
    Route::post('/sync', [CurrencyController::class, 'sync']);
});

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'status' => 'success',
        'endpoints' => [
            '/api/countries',
            '/api/risk',
            '/api/ports',
            '/api/news',
            '/api/currency'
        ]
    ]);
});

Route::post('/risk/recalculate-all', [RiskController::class, 'recalculateAll']);

Route::get('/test-risk', function () {
    try {
        $country = App\Models\Country::where('code', 'IDN')->first();
        $service = app(App\Services\RiskScoreService::class);
        $result = $service->calculateRiskScore($country);
        return response()->json(['success' => true, 'data' => $result]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});
