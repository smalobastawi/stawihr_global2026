<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EnsureValidLicence
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = \Auth::user();

        // Perform action
        if ($user != null) {

            $checkLicence = Http::post('https://licensesvr.stawitech.com/api/licence/validate', [
                'domain' => $_SERVER['SERVER_NAME'],
                'license_key'=>config('app.license_key'),
            ]);
            $check = $checkLicence->json();

            if ($check == null) {
                \Auth::logout();
                return redirect()->route('invalidLicense');
            } else {
                if ($check['status'] == 'active') {

                    return $response;
                } else {
                    \Auth::logout();
                    return redirect()->route('invalidLicense');
                }
            }
        }

        return $response;

    }
}
