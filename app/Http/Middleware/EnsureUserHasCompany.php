<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasCompany
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Allow if not logged in (e.g., guest routes)
        if (!$user) {
            return $next($request);
        }

        // Allow SuperAdmin bypass
        if ($user->hasRole('SuperAdmin')) {
            return $next($request);
        }

        // Get employee details
        $employee = $user->employeeDetails;

        // Check if user has a company_id OR if employee has a company_id
        // Fixed: Used proper OR condition (|| instead of |) and null checks
        if (!$user->company_id && (!$employee || !$employee->company_id)) {

            // Check if user has any company permissions through permitted companies
            $hasCompanyPermissions = $user->PermittedCompanies()->exists();

            if (!$hasCompanyPermissions) {
                abort(403, "You must be assigned to a company to access this resource.");
            }
        }

        return $next($request);
    }
}
