<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/ports', [DashboardController::class, 'ports'])->name('ports');