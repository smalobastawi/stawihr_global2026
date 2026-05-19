<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeEarningsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'employee_id' => 'required|exists:employee,employee_id',
            'payroll_earning_type_id' => 'required|exists:payroll_earning_types,id',
           // 'earning_name' => 'required|string|max:255',
            'earning_category' => 'required|in:basic_salary,allowance,bonus,overtime,commission,other',
            'calculation_type' => 'required|in:fixed_amount,percentage_of_basic,percentage_of_gross,hourly_rate,daily_rate',
            'units' => 'nullable|integer|min:0',
            'limit_per_month' => 'nullable|numeric|min:0',
            'limit_per_year' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'payroll_year' => 'required|integer|min:2020|max:2050',
            'payroll_month' => 'required|integer|min:1|max:12',
            'frequency' => 'required|in:monthly,weekly,bi_weekly,quarterly,annually,one_time',
            'is_taxable' => 'boolean',
            'is_pensionable' => 'boolean',
            'is_recurring' => 'boolean',
            'description' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:100',
        ];

        // Add conditional validation based on calculation type
        switch ($this->calculation_type) {
            case 'fixed_amount':
                $rules['amount'] = 'required|numeric|min:0';
                break;
            case 'percentage_of_basic':
            case 'percentage_of_gross':
                $rules['percentage'] = 'required|numeric|min:0|max:100';
                break;
            case 'hourly_rate':
            case 'daily_rate':
                $rules['rate'] = 'required|numeric|min:0';
                break;
        }

        // Add status validation for update requests
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = 'required|in:active,inactive,suspended,expired';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'employee_id.required' => __('employee_earnings.employee_required'),
            'employee_id.exists' => __('employee_earnings.employee_not_found'),
            'payroll_earning_type_id.required' => __('employee_earnings.earning_type_required'),
            'payroll_earning_type_id.exists' => __('employee_earnings.earning_type_not_found'),
            'earning_name.required' => __('employee_earnings.earning_name_required'),
            'earning_category.required' => __('employee_earnings.earning_category_required'),
            'calculation_type.required' => __('employee_earnings.calculation_type_required'),
            'amount.required' => __('employee_earnings.amount_required'),
            'amount.numeric' => __('employee_earnings.amount_must_be_positive'),
            'amount.min' => __('employee_earnings.amount_must_be_positive'),
            'percentage.required' => __('employee_earnings.percentage_required'),
            'percentage.numeric' => __('employee_earnings.percentage_must_be_valid'),
            'percentage.min' => __('employee_earnings.percentage_must_be_valid'),
            'percentage.max' => __('employee_earnings.percentage_must_be_valid'),
            'rate.required' => __('employee_earnings.rate_required'),
            'rate.numeric' => __('employee_earnings.rate_must_be_positive'),
            'rate.min' => __('employee_earnings.rate_must_be_positive'),
            'units.integer' => __('employee_earnings.units_must_be_positive'),
            'units.min' => __('employee_earnings.units_must_be_positive'),
            'limit_per_month.numeric' => __('employee_earnings.limit_must_be_positive'),
            'limit_per_month.min' => __('employee_earnings.limit_must_be_positive'),
            'limit_per_year.numeric' => __('employee_earnings.limit_must_be_positive'),
            'limit_per_year.min' => __('employee_earnings.limit_must_be_positive'),
            'effective_from.required' => __('employee_earnings.effective_from_required'),
            'effective_to.after' => __('employee_earnings.effective_to_after_from'),
            'payroll_year.required' => __('employee_earnings.payroll_year_required'),
            'payroll_month.required' => __('employee_earnings.payroll_month_required'),
            'frequency.required' => __('employee_earnings.frequency_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'employee_id' => __('employee_earnings.employee'),
            'payroll_earning_type_id' => __('employee_earnings.payroll_earning_type'),
            'earning_name' => __('employee_earnings.earning_name'),
            'earning_category' => __('employee_earnings.earning_category'),
            'calculation_type' => __('employee_earnings.calculation_type'),
            'amount' => __('employee_earnings.amount'),
            'percentage' => __('employee_earnings.percentage'),
            'rate' => __('employee_earnings.rate'),
            'units' => __('employee_earnings.units'),
            'limit_per_month' => __('employee_earnings.limit_per_month'),
            'limit_per_year' => __('employee_earnings.limit_per_year'),
            'effective_from' => __('employee_earnings.effective_from'),
            'effective_to' => __('employee_earnings.effective_to'),
            'payroll_year' => __('employee_earnings.payroll_year'),
            'payroll_month' => __('employee_earnings.payroll_month'),
            'frequency' => __('employee_earnings.frequency'),
            'description' => __('employee_earnings.description'),
            'reference_number' => __('employee_earnings.reference_number'),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert checkbox values to boolean
        $this->merge([
            'is_taxable' => $this->has('is_taxable'),
            'is_pensionable' => $this->has('is_pensionable'),
            'is_recurring' => $this->has('is_recurring'),
        ]);

        // Set default values for optional fields based on calculation type
        if ($this->calculation_type !== 'fixed_amount') {
            $this->merge(['amount' => null]);
        }
        if (!in_array($this->calculation_type, ['percentage_of_basic', 'percentage_of_gross'])) {
            $this->merge(['percentage' => null]);
        }
        if (!in_array($this->calculation_type, ['hourly_rate', 'daily_rate'])) {
            $this->merge(['rate' => null]);
        }
    }
}