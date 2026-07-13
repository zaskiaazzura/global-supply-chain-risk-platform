<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\WatchlistController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PortController as AdminPortController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;

// ============================================
// AUTH ROUTES (TIDAK PERLU LOGIN)
// ============================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================
// WEB ROUTES (HARUS LOGIN)
// ============================================
// ============================================
// PUBLIC DASHBOARD (HARUS LOGIN)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ports', [DashboardController::class, 'ports'])->name('ports');
    Route::get('/comparison', [ComparisonController::class, 'index'])->name('comparison');
    Route::get('/api/compare', [ComparisonController::class, 'compare']);
});

// Watchlist Routes
Route::middleware('auth')->group(function () {
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist');
    Route::post('/watchlist/toggle', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');
    Route::delete('/watchlist/{id}', [WatchlistController::class, 'remove'])->name('watchlist.remove');    
    // Check status favorit (via API)
    Route::get('/api/watchlist/check/{code}', [WatchlistController::class, 'check']);
});

// ============================================
// ADMIN ROUTES (HARUS LOGIN + ROLE ADMIN)
// ============================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('ports', AdminPortController::class);
    Route::resource('articles', AdminArticleController::class);
});