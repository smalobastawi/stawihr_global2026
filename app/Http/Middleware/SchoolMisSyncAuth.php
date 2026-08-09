<?php

namespace App\Http\Middleware;

use App\Services\Integrations\SchoolMisSettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolMisSyncAuth
{
    public function __construct(private readonly SchoolMisSettingService $settings) {}

    public function handle(Request $request, Closure $next): Response
    {
        $settings = $this->settings->current();

        if (! $settings->is_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'School MIS integration is disabled on this HR instance.',
            ], 403);
        }

        $providedToken = $request->header('X-API-Token')
            ?? $request->header('Authorization');

        if (is_string($providedToken) && str_starts_with($providedToken, 'Bearer ')) {
            $providedToken = substr($providedToken, 7);
        }

        if (! $this->settings->tokenMatches((string) $providedToken, $settings)) {
            $hasConfiguredToken = $settings->hasPullApiToken()
                || filled(config('portal.internal_api_token'));

            if (! $hasConfiguredToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'School MIS sync API is not configured. Generate an API key in Settings → School MIS Integration.',
                ], 503);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized School MIS sync request.',
            ], 401);
        }

        $request->attributes->set('school_mis_settings', $settings);

        return $next($request);
    }
}
