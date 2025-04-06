<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountStatementController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Simple test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Debug route to verify API is working
Route::get('/debug', function () {
    return response()->json([
        'message' => 'API Debug Route',
        'timestamp' => now(),
        'routes' => Route::getRoutes()->getRoutes()
    ]);
});

// Account Statement API
Route::post('/v1/statements', [AccountStatementController::class, 'apiGenerate']);
    // ->name('api.account-statement');
    // ->middleware('api'); 