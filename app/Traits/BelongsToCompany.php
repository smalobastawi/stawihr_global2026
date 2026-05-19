<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        // ----------------------------
        // APPLY GLOBAL COMPANY SCOPE
        // ----------------------------
        static::addGlobalScope('company', function (Builder $builder) {
            $user = auth()->user();

            if (!$user) {
                return; // Guest or CLI (e.g., queue workers)
            }

            // SUPERADMIN BYPASS
            if ($user->hasRole('SuperAdmin')) {
                return;
            }

            // Check if user has company permissions
            $permittedCompanies = $user->PermittedCompanies()->pluck('company_id')->toArray();

            if (!empty($permittedCompanies)) {
                // User has specific company permissions, restrict to those companies
                $builder->whereIn($builder->getModel()->getTable() . '.company_id', $permittedCompanies);
            } else {
                // Fall back to default behavior: request > user's company
                $companyId = request()->get('company_id', $user->company_id);
                $builder->where($builder->getModel()->getTable() . '.company_id', $companyId);
            }
        });

        // ----------------------------
        // SET COMPANY ID ON CREATE
        // ----------------------------
        static::creating(function ($model) {
            $user = auth()->user();
            if (!$user) {
                return;
            }

            // Do not overwrite manually set value
            if (!$model->company_id) {
                // Check if user has company permissions
                $permittedCompanies = $user->PermittedCompanies()->pluck('company_id')->toArray();

                if (!empty($permittedCompanies)) {
                    // If user has permissions for multiple companies, use the first one or from request
                    $companyId = request()->get('company_id');
                    if ($companyId && in_array($companyId, $permittedCompanies)) {
                        $model->company_id = $companyId;
                    } else {
                        $model->company_id = $permittedCompanies[0]; // Default to first permitted company
                    }
                } else {
                    // Fall back to default behavior
                    $model->company_id = request()->get('company_id', $user->company_id);
                }
            }
        });

        // ----------------------------
        // SET COMPANY ID ON UPDATE
        // ----------------------------
        static::updating(function ($model) {
            $user = auth()->user();
            if (!$user) {
                return;
            }

            // Do not override existing company_id UNLESS none is set
            if (!$model->company_id) {
                // Check if user has company permissions
                $permittedCompanies = $user->PermittedCompanies()->pluck('company_id')->toArray();

                if (!empty($permittedCompanies)) {
                    // If user has permissions for multiple companies, use the first one or from request
                    $companyId = request()->get('company_id');
                    if ($companyId && in_array($companyId, $permittedCompanies)) {
                        $model->company_id = $companyId;
                    } else {
                        $model->company_id = $permittedCompanies[0]; // Default to first permitted company
                    }
                } else {
                    // Fall back to default behavior
                    $model->company_id = request()->get('company_id', $user->company_id);
                }
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}