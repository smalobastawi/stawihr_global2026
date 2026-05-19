<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecruitmentSetting extends Model
{
    use HasFactory;

    protected $table = 'recruitment_settings';
    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'setting_key',
        'setting_name',
        'setting_value',
        'setting_group',
        'description',
        'is_active',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Setting groups
    const GROUP_APPROVAL = 'approval';
    const GROUP_WORKFLOW = 'workflow';
    const GROUP_NOTIFICATIONS = 'notifications';
    const GROUP_EVALUATION = 'evaluation';
    const GROUP_EMAIL_TEMPLATES = 'email_templates';

    /**
     * Get the user who last updated this setting
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('setting_group', $group);
    }

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->where('is_active', true)->first();
        return $setting ? $setting->setting_value : $default;
    }

    /**
     * Set a setting value
     */
    public static function setValue($key, $value, $updatedBy = null)
    {
        $setting = self::where('setting_key', $key)->first();

        if ($setting) {
            $setting->setting_value = $value;
            if ($updatedBy) {
                $setting->updated_by = $updatedBy;
            }
            $setting->save();
            return $setting;
        }

        return null;
    }

    /**
     * Get all settings as array
     */
    public static function getAllSettings()
    {
        return self::active()->get()->mapWithKeys(function ($setting) {
            return [$setting->setting_key => $setting->setting_value];
        })->toArray();
    }

    /**
     * Get settings by group
     */
    public static function getSettingsByGroup($group)
    {
        return self::byGroup($group)->active()->get();
    }

    /**
     * Check if multi-level approval is enabled
     */
    public static function isMultiLevelApprovalEnabled()
    {
        return self::getValue('approval_workflow_enabled', 'true') === 'true';
    }

    /**
     * Get salary thresholds for approvals
     */
    public static function getApprovalThresholds()
    {
        return [
            'finance' => (float) self::getValue('finance_approval_threshold', 100000),
            'md' => (float) self::getValue('md_approval_threshold', 200000),
        ];
    }

    /**
     * Check if auto-conversion is enabled
     */
    public static function isAutoConversionEnabled()
    {
        return self::getValue('auto_convert_to_job', 'false') === 'true';
    }

    /**
     * Get default job post duration
     */
    public static function getDefaultJobPostDuration()
    {
        return (int) self::getValue('default_job_post_duration', 30);
    }

    /**
     * Get minimum evaluation score
     */
    public static function getMinimumEvaluationScore()
    {
        return (float) self::getValue('min_evaluation_score', 6.0);
    }
}
