<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EndtimeDashboardController;
use App\Http\Controllers\LotLookupController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Authenticated user routes
Route::middleware(['auth', \App\Http\Middleware\CheckUserStatus::class])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Production pages
    Route::get('/endtime', [App\Http\Controllers\EndtimePageController::class, 'index'])->name('endtime');

    Route::get('/escalation', function () {
        return view('pages.escalation');
    })->name('escalation');

    // Lot lookup API
    Route::post('/api/lot-lookup', [LotLookupController::class, 'lookup'])->name('lot.lookup');

    // Machine lookup API
    Route::post('/api/machine-lookup', [EndtimeDashboardController::class, 'lookupMachine'])->name('machine.lookup');

    // Save endtime API
    Route::post('/api/save-endtime', [EndtimeDashboardController::class, 'saveEndtime'])->name('endtime.save');
});

// API routes have been moved to routes/api.php

// Add these routes for your offline password reset
Route::get('password/reset', [App\Http\Controllers\Auth\OfflinePasswordResetController::class, 'showResetForm'])
    ->name('password.request');
Route::post('password/reset', [App\Http\Controllers\Auth\OfflinePasswordResetController::class, 'reset'])
    ->name('password.reset.offline');

