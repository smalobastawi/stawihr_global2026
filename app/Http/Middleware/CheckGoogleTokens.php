<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\GoogleFormService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckGoogleTokens
{
    protected $googleFormService;

    public function __construct(GoogleFormService $googleFormService)
    {
        $this->googleFormService = $googleFormService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        // Skip if not authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // If we have a session token, use that
        if (Session::has('google_access_token')) {
            return $next($request);
        }

        // Check if user has stored tokens
        if ($user->google_access_token) {
            $tokenData = [
                'access_token' => $user->google_access_token,
                'refresh_token' => $user->google_refresh_token,
                'expires_in' => $user->google_token_expires_at->diffInSeconds(now()),
                'created' => $user->google_token_expires_at->subSeconds($user->google_token_expires_at->diffInSeconds(now()))->timestamp,
            ];

            // Set the token in the service to check if it's still valid
            if ($this->googleFormService->setAccessToken($tokenData)) {
                Session::put('google_access_token', $tokenData);
                return $next($request);
            }
        }

        // If we get here, tokens are invalid/missing
        if ($request->ajax() || $request->wantsJson()) {
            return response('Google authentication required', 401);
        }

        return redirect()->route('survey.google.auth')->with('error', 'Please re-authenticate with Google to access surveys.');
    }
}
