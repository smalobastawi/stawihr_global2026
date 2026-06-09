<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class FinancialYear extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'uuid',
        'start_date',
        'end_date',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'start_date',
        'end_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($financialYear) {
            if ($financialYear->status == 1 && $financialYear->company_id) {
                static::withoutGlobalScopes()
                    ->where('company_id', $financialYear->company_id)
                    ->where('status', 1)
                    ->update(['status' => 0]);
            }
        });

        static::updating(function ($financialYear) {
            if ($financialYear->isDirty('status') && $financialYear->status == 1 && $financialYear->company_id) {
                static::withoutGlobalScopes()
                    ->where('company_id', $financialYear->company_id)
                    ->where('status', 1)
                    ->where('id', '!=', $financialYear->id)
                    ->update(['status' => 0]);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public static function activeForCompany(?int $companyId = null): ?self
    {
        $companyId = $companyId ?? \App\Support\CompanyContext::defaultCompanyIdForNewRecord();

        $query = static::withoutGlobalScope('company')->where('status', 1);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->first();
    }

    public function getFormattedDateRangeAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_date)->format('M d, Y');
        $end = \Carbon\Carbon::parse($this->end_date)->format('M d, Y');
        return "$start - $end";
    }
}
