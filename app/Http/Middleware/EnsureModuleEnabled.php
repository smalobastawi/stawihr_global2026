<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $route = $request->route();
        if (!$route) {
            return $next($request);
        }

        $moduleName = $route->action['module'] ?? null;

        if (!$moduleName || moduleEnabled($moduleName)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'This module is not enabled.'], 403);
        }

        abort(403, 'This module is not enabled.');
    }
}
