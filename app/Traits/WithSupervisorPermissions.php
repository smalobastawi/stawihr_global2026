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

            // If no employee details, restrict all access
            if (!$user->employeeDetails) {
                $builder->whereRaw('1=0');
                return;
            }

            $employeeId = $user->employeeDetails->employee_id;

            // Always resolve current subordinates from DB so supervisor reassignment
            // immediately grants visibility of prior leave applications.
            $subordinateIds = \App\Models\Employee::query()
                ->where('supervisor_id', $employeeId)
                ->pluck('employee_id');

            // Include both the supervisor's ID and subordinate IDs
            $allowedIds = $subordinateIds->push($employeeId)->unique()->values();

            $builder->whereIn($builder->getModel()->getTable() . '.employee_id', $allowedIds);
        });
    }
}