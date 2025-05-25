<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EndtimeDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endtime Dashboard API Routes
// Dashboard values
Route::get('/get-target-capacity', [EndtimeDashboardController::class, 'getTargetCapacity']);
Route::get('/get-performance-target', [EndtimeDashboardController::class, 'getPerformanceTarget']);
Route::get('/total-endtime', [EndtimeDashboardController::class, 'getEndtimeTotal']);
Route::get('/total-endtime-by-cutoff', [EndtimeDashboardController::class, 'getEndtimeTotalByCutoff']);
Route::get('/submitted-total', [EndtimeDashboardController::class, 'getSubmittedTotal']);
Route::get('/remaining-total', [EndtimeDashboardController::class, 'getRemainingTotal']);

// Chart data
Route::get('/chart/line-production', [EndtimeDashboardController::class, 'getLineProduction']);
Route::get('/chart/line-target', [EndtimeDashboardController::class, 'getLineTarget']);
Route::get('/chart/size-production', [EndtimeDashboardController::class, 'getSizeProduction']);
Route::get('/chart/size-target', [EndtimeDashboardController::class, 'getSizeTarget']);

// Save submitted lots
Route::post('/save-submitted', [EndtimeDashboardController::class, 'saveSubmitted']);

// Save auto-refresh state
Route::post('/save-auto-refresh-state', [EndtimeDashboardController::class, 'saveAutoRefreshState']);

// Process WIP data
Route::post('/process-wip-data', [EndtimeDashboardController::class, 'processWipData']);
