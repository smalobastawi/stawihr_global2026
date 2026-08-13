<?php

namespace App\Models\Performance;

use App\Support\CompanyContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PerformanceSetting extends Model
{
    public const APPROACH_HR_DEFINED = 'hr_defined';
    public const APPROACH_STAFF_DEFINED = 'staff_defined';

    protected $table = 'performance_settings';
    protected $primaryKey = 'performance_setting_id';

    protected $fillable = [
        'company_id',
        'appraisal_approach',
        'policy_notes',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id', 'id');
    }

    public function isHrDefined(): bool
    {
        return $this->appraisal_approach === self::APPROACH_HR_DEFINED;
    }

    public function isStaffDefined(): bool
    {
        return $this->appraisal_approach === self::APPROACH_STAFF_DEFINED;
    }

    public static function current(?int $companyId = null): self
    {
        $companyId = $companyId
            ?? CompanyContext::sessionCompanyId()
            ?? Auth::user()?->company_id;

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
                'appraisal_approach' => self::APPROACH_HR_DEFINED,
                'policy_notes' => null,
            ]);
    }

    public static function approaches(): array
    {
        return [
            self::APPROACH_HR_DEFINED => 'HR-defined performance areas & metrics',
            self::APPROACH_STAFF_DEFINED => 'Staff-defined goals, objectives & metrics',
        ];
    }
}
