<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountStatementController;
use App\Http\Middleware\ApiKeyMiddleware;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Simple test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

// Debug route to verify API is working
Route::get('/debug', function (Request $request) {
    return response()->json([
        'message' => 'API Debug Route',
        'timestamp' => now(),
        'request_method' => $request->method(),
        'is_ajax' => $request->ajax(),
        'accepts_json' => $request->expectsJson(),
        'content_type' => $request->header('Content-Type'),
        'middleware' => app('router')->getRoutes()->match($request)->middleware(),
        'route_uri' => app('router')->getRoutes()->match($request)->uri()
    ]);
});

// Add a debug route for statements
Route::post('/v1/statements-debug', function (Request $request) {
    return response()->json([
        'message' => 'Statement debug route hit',
        'received_data' => $request->all()
    ]);
});

// Test GET route for statements
Route::get('/v1/statements-test', function () {
    return response()->json(['message' => 'GET statements test route works']);
});

// Account Statement API - with API Key Middleware
Route::post('/v1/statements', [AccountStatementController::class, 'apiGenerate'])
    ->middleware([ApiKeyMiddleware::class]);
    // ->name('api.account-statement'); 