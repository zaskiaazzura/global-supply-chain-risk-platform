<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/ports', [DashboardController::class, 'ports'])->name('ports.page');
Route::get('/comparison', [DashboardController::class, 'comparison'])->name('comparison.page');