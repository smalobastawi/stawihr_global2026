<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BranchPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view the branch.
     */
    public function view(User $user, Location $location)
    {
        return $user->locations->contains($location) ||
            $user->branchPermissions->where('name', 'view')
            ->where('location_id', $location->id)
            ->exists();
    }

    /**
     * Determine whether the user can create a branch data.
     */
    public function create(User $user)
    {
        return $user->branchPermissions->where('name', 'create')->exists();
    }

    /**
     * Determine whether the user can update the branch.
     */
    public function update(User $user, Location $location)
    {
        return $user->locations->contains($location) ||
            $user->branchPermissions->where('name', 'edit')
            ->where('location_id', $location->id)
            ->exists();
    }

    /**
     * Determine whether the user can delete the branch.
     */
    public function delete(User $user, Location $location)
    {
        return $user->branchPermissions->where('name', 'delete')
            ->where('location_id', $location->id)
            ->exists();
    }

    /**
     * Determine whether the user can restore the branch.
     */
    public function restore(User $user, Location $location)
    {
        return $user->branchPermissions->where('name', 'restore')
            ->where('location_id', $location->id)
            ->exists();
    }

    /**
     * Determine whether the user can permanently delete the branch.
     */
    public function forceDelete(User $user, Location $location)
    {
        return $user->branchPermissions->where('name', 'force_delete')
            ->where('location_id', $location->id)
            ->exists();
    }

    /**
     * Determine whether the user can approve branch-related actions.
     */
    public function approve(User $user, Location $location)
    {
        return $user->branchPermissions->where('name', 'approve_branch_data')
            ->where('location_id', $location->id)
            ->exists();
    }

    public function viewAny(User $user, $branchId = null)
    {
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        $permittedBranchIds = [$user->location_id];

        if (is_null($branchId)) {
            // Allow access if the user has a branch assigned
            return !empty($permittedBranchIds);
        }

        // Check if the user has access to the specified branch
        return in_array($branchId, $permittedBranchIds);
    }
}
