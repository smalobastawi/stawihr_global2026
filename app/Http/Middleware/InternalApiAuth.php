<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = config('portal.internal_api_token');

        if (empty($expectedToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Internal API is not configured on this instance.',
            ], 503);
        }

        $providedToken = $request->header('X-API-Token')
            ?? $request->header('Authorization');

        if (is_string($providedToken) && str_starts_with($providedToken, 'Bearer ')) {
            $providedToken = substr($providedToken, 7);
        }

        if (!hash_equals($expectedToken, (string) $providedToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized internal API request.',
            ], 401);
        }

        return $next($request);
    }
}
