<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\RiskController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CurrencyController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/risk', [RiskController::class, 'index']);
Route::get('/ports', [PortController::class, 'index']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/currency', [CurrencyController::class, 'index']);