<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveGroupSettingRequest extends FormRequest

{
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * Filter out settings that are not active (unchecked status)
     */
    protected function prepareForValidation(): void
    {
        $settings = $this->input('settings', []);

        // Filter out inactive settings (where active is not checked)
        $activeSettings = [];
        foreach ($settings as $leaveTypeId => $settingData) {
            if (!empty($settingData['active'])) {
                $activeSettings[$leaveTypeId] = $settingData;
            }
        }

        // Replace settings with only active ones
        $this->merge([
            'settings' => $activeSettings,
        ]);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',

            'settings' => 'required|array',
            'settings.*.applicable_on' => 'required|in:calendar_days,working_days',
            'settings.*.accrual_frequency' => 'required|in:monthly,weekly,daily,once',
            'settings.*.annual_entitlement' => 'required|integer|min:0',
            'settings.*.carryover_days' => 'required|integer|min:0',
            'settings.*.max_carryover_days' => 'nullable|integer|min:0',
            'settings.*.earning_rate' => 'required|numeric|min:0',
            'settings.*.gender' => 'required|in:male,female,all',
            'settings.*.probation_period_days' => 'required|integer|min:0',
            'settings.*.notice_period_days' => 'required|integer|min:0',
            'settings.*.allow_half_day' => 'boolean',
            'settings.*.paid' => 'boolean',
            'settings.*.active' => 'boolean',
            'settings.*.max_consecutive_days' => 'nullable|integer|min:0',
            'settings.*.allow_advanced_leave' => 'boolean',
            'settings.*.advanced_period_months' => 'nullable|integer|min:1|max:12',
            'settings.*.advanced_limit_days' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'settings.*.applicable_on.required' => 'Applicable on field is required',
            'settings.*.accrual_frequency.required' => 'Accrual frequency is required',
            'settings.*.annual_entitlement.required' => 'Annual entitlement is required',
            // Add more custom messages as needed
        ];
    }
}
