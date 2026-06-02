<?php

namespace App\Http\Middleware;

use App\Support\CompanyContext;
use Closure;
use Illuminate\Support\Facades\Auth;

class SetActiveCompany
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Do not inject the current session company into the switch POST body —
        // that would overwrite the newly selected company_id from the form.
        if ($request->routeIs('company.switch')) {
            return $next($request);
        }

        $activeCompany = CompanyContext::activeCompany();
        if ($activeCompany) {
            $request->attributes->set('company', $activeCompany);

            if (!$request->has('company_id')) {
                $request->merge(['company_id' => $activeCompany->id]);
            }
        }

        return $next($request);
    }
}
