<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait WithSupervisorPermissions
{
    protected static function bootWithSupervisorPermissions()
    {
        static::addGlobalScope('supervisorPermissions', function (Builder $builder) {
            $user = Auth::user();
            //Skip restriction for unauthenticated users in case of API calls
            if (!$user) {
                return;
            }
            
            // Skip restriction for admin/super admin users
            if ($user->hasRole(['SuperAdmin', 'HR Administrator'])) {
                return;
            }

            // Load employeeDetails if not already loaded
            if (!$user->relationLoaded('employeeDetails')) {
                $user->load('employeeDetails.subordinates');
            }

            // If no employee details, restrict all access
            if (!$user->employeeDetails) {
                $builder->whereRaw('1=0');
                return;
            }

            $employeeId = $user->employeeDetails->employee_id;
            $subordinateIds = $user->employeeDetails->subordinates->pluck('employee_id');
            
            // Include both the supervisor's ID and subordinate IDs
            $allowedIds = $subordinateIds->push($employeeId)->unique();

            // For models with employee_id column
            if (isset($builder->getModel()->employee_id)) {
                $builder->whereIn('employee_id', $allowedIds);
            } 
            // For Employee model itself
            else {
                $builder->whereIn('employee_id', $allowedIds);
            }
        });
    }
}