<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class SetActiveCompany
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // If SuperAdmin, use session-selected company
        if ($user->hasRole('SuperAdmin')) {
            $activeCompanyID = session('active_company_id');

            if ($activeCompanyID) {
                $request->attributes->set('company', Company::find($activeCompanyID));
            }
        } else {
            // Normal user uses assigned company_id
            $request->attributes->set('company', $user->company);
        }

        return $next($request);
    }
}
