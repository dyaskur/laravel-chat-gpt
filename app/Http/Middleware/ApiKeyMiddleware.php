<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $validKey = config('app.api_key');

        if (! $apiKey || $apiKey !== $validKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
