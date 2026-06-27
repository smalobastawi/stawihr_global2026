<?php

namespace App\Models\Pdp;

use Illuminate\Database\Eloquent\Model;

class PdpSetting extends Model
{
    protected $table = 'pdp_settings';
    protected $primaryKey = 'pdp_setting_id';

    protected $fillable = [
        'company_id',
        'default_review_frequency',
        'allow_employee_self_service',
        'require_supervisor_approval',
        'require_hr_review',
        'policy_notes',
    ];

    protected $casts = [
        'allow_employee_self_service' => 'boolean',
        'require_supervisor_approval' => 'boolean',
        'require_hr_review' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id', 'id');
    }

    public static function current(?int $companyId = null): self
    {
        $query = static::query();

        if ($companyId) {
            $setting = (clone $query)->where('company_id', $companyId)->first();
            if ($setting) {
                return $setting;
            }
        }

        return (clone $query)->whereNull('company_id')->first()
            ?? static::create([
                'company_id' => $companyId,
                'default_review_frequency' => 'quarterly',
                'allow_employee_self_service' => true,
                'require_supervisor_approval' => true,
                'require_hr_review' => false,
            ]);
    }
}
