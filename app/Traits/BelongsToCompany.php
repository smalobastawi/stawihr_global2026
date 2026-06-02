<?php

namespace App\Traits;

use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (!CompanyContext::user()) {
                return;
            }

            $scopedCompanyIds = CompanyContext::scopedCompanyIds();
            if ($scopedCompanyIds === null) {
                return;
            }

            if (empty($scopedCompanyIds)) {
                return;
            }

            $column = $builder->getModel()->getTable() . '.company_id';

            if (count($scopedCompanyIds) === 1) {
                $builder->where($column, $scopedCompanyIds[0]);
            } else {
                $builder->whereIn($column, $scopedCompanyIds);
            }
        });

        static::creating(function ($model) {
            if (!CompanyContext::user() || $model->company_id) {
                return;
            }

            $companyId = CompanyContext::defaultCompanyIdForNewRecord();
            if ($companyId) {
                $model->company_id = $companyId;
            }
        });

        static::updating(function ($model) {
            if (!CompanyContext::user() || $model->company_id) {
                return;
            }

            $companyId = CompanyContext::defaultCompanyIdForNewRecord();
            if ($companyId) {
                $model->company_id = $companyId;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
