<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait WithBranchPermissions
{
    /**
     * Boot the trait and add the global scope.
     */
    protected static function bootWithBranchPermissions()
    {
        // Check if the branch_permissions table exists and has entries
        if (!\Schema::hasTable('branch_permissions') || !DB::table('branch_permissions')->exists()) {
            return;
        }

        static::addGlobalScope('branchPermissions', function (Builder $builder) {
            $user = Auth::user();

            // Skip if no user is authenticated
            if (!$user) {
                return;
            }

            // Skip for SuperAdmins
            if ($user->hasRole('SuperAdmin')) {
                return;
            }

            // Get the employee record for this user
            $employee = $builder->getModel()
                ->getConnection()
                ->table('employee')
                ->where('user_id', $user->id)
                ->first();

            // If employee has a direct location_id, filter by that
            if ($employee && $employee->location_id) {
                $builder->where('location_id', $employee->location_id);
            } 
            // Otherwise check branch permissions
            else {
                $permittedBranchIds = DB::table('branch_permissions')
                    ->where('user_id', $user->id)
                    ->pluck('location_id')
                    ->toArray();

                if (!empty($permittedBranchIds)) {
                    $builder->whereIn('location_id', $permittedBranchIds);
                } else {
                    // If no permissions, return no results
                    $builder->whereRaw('1 = 0');
                }
            }
        });
    }
}