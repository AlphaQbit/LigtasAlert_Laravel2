<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/alerts', [AlertController::class, 'index']);
Route::post('/alerts', [AlertController::class, 'store']);
Route::put('/alerts/{id}', [AlertController::class, 'update']);
Route::post('/alerts/{id}/respond', [AlertController::class, 'respond']);

Route::get('/facilities', [FacilityController::class, 'index']);
Route::get('/stats', [StatsController::class, 'index']);

Route::get('/', function () {
    return response()->json([
        'service' => 'LigtasAlert API',
        'version' => '1.0',
        'endpoints' => [
            'GET /api/alerts' => 'List all alerts',
            'GET /api/alerts?status=active' => 'List active alerts',
            'POST /api/alerts' => 'Create new alert',
            'PUT /api/alerts/{id}' => 'Update alert',
            'POST /api/alerts/{id}/respond' => 'Add responder',
            'GET /api/facilities' => 'List facilities',
            'GET /api/stats' => 'Get dashboard stats',
        ],
    ]);
});
