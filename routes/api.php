<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubmisiCoreController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/countries', [SubmisiCoreController::class, 'getCountries']);
Route::get('/ports', [SubmisiCoreController::class, 'getPorts']);