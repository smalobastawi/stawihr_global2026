<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Providers;

use App\Models\Employee;
use App\Policies\BranchPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Employee::class => BranchPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if (is_string($ability) && str_starts_with($ability, 'pmMenu__')) {
                $moduleName = substr($ability, strlen('pmMenu__'));

                if (!moduleEnabled($moduleName)) {
                    return false;
                }
            }

            return null;
        });

        Gate::after(function ($user, $ability) {
            ($user->hasRole('SuperAdmin'));
            return $user->hasRole('SuperAdmin') ? true : null;
        });

        Gate::define('view', [BranchPolicy::class, 'view']);
        Gate::define('create', [BranchPolicy::class, 'create']);
        Gate::define('update', [BranchPolicy::class, 'update']);
        Gate::define('delete', [BranchPolicy::class, 'delete']);
        Gate::define('restore', [BranchPolicy::class, 'restore']);
        Gate::define('force-delete', [BranchPolicy::class, 'forceDelete']);
        Gate::define('approve', [BranchPolicy::class, 'approve']);

        
    }
    
}
