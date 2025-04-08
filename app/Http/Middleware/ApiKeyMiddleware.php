<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get API key from request header
        $apiKey = $request->header('X-API-KEY');
        
        // Check if API key exists
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is missing'
            ], 401);
        }
        
        // Check if API key matches
        if ($apiKey !== env('API_KEY')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 401);
        }
        
        return $next($request);
    }
}
