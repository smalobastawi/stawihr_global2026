<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRequisitionRequest extends FormRequest
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

    protected function prepareForValidation(): void
    {
        if ($this->input('requisition_type') !== 'replacement') {
            $this->merge([
                'replaced_employee_name' => null,
                'replacement_reason' => null,
                'replacement_reason_other' => null,
            ]);

            return;
        }

        if ($this->input('replacement_reason') !== 'other') {
            $this->merge([
                'replacement_reason_other' => null,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'position_title' => 'required|string|max:200',
            'job_description' => 'required',
            'job_requirements' => 'required',
            'number_of_positions' => 'required|integer|min:1',
            'job_type' => 'required|string',
            'employment_type' => 'required|string',
            'location_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'work_location' => 'nullable|string|max:255',
            'proposed_start_date' => 'nullable|date',
            'minimum_salary' => 'nullable|numeric|min:0',
            'maximum_salary' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'other_benefits' => 'nullable|string',
            'required_by_date' => 'required|date',
            'urgency_level' => 'required|string|in:low,normal,high,critical',
            'reason_for_requisition' => 'required',
            'requisition_type' => 'required|string|in:new_position,replacement',
            'replaced_employee_name' => 'nullable|string|max:200',
            'replacement_reason' => 'nullable|string|in:resignation,termination,transfer,other',
            'replacement_reason_other' => 'nullable|string|max:255',
            'budget_justification' => 'nullable',
            'justification_for_hire' => 'nullable|string',
            'reporting_manager' => 'required|string|max:100',
            'key_responsibilities' => 'nullable|string',
            'minimum_qualifications' => 'nullable|string',
            'experience_required' => 'nullable|string|max:255',
            'skills_competencies' => 'nullable|string',
            'recruitment_source' => 'required|string|in:internal,external,both',
        ];

        // Additional validation when minimum and maximum salary are provided
        $rules['minimum_salary'] = 'nullable|numeric|min:0';
        $rules['maximum_salary'] = 'nullable|numeric|min:0';

        if ($this->has('minimum_salary') && $this->has('maximum_salary')) {
            if ($this->minimum_salary && $this->maximum_salary) {
                $rules['maximum_salary'] = 'numeric|min:' . $this->minimum_salary;
            }
        }

        // Replacement fields only apply when hiring to replace an existing employee
        if ($this->input('requisition_type') === 'replacement') {
            $rules['replaced_employee_name'] = 'required|string|max:200';
            $rules['replacement_reason'] = 'required|string|in:resignation,termination,transfer,other';

            if ($this->input('replacement_reason') === 'other') {
                $rules['replacement_reason_other'] = 'required|string|max:255';
            }
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'position_title.required' => 'Position title is required.',
            'position_title.max' => 'Position title cannot exceed 200 characters.',
            'job_description.required' => 'Job description is required.',
            'job_requirements.required' => 'Job requirements are required.',
            'number_of_positions.required' => 'Number of positions is required.',
            'number_of_positions.integer' => 'Number of positions must be a whole number.',
            'number_of_positions.min' => 'Number of positions must be at least 1.',
            'job_type.required' => 'Job type is required.',
            'employment_type.required' => 'Employment type is required.',
            'location_id.integer' => 'Please select a valid location.',
            'department_id.integer' => 'Please select a valid department.',
            'minimum_salary.numeric' => 'Minimum salary must be a number.',
            'minimum_salary.min' => 'Minimum salary cannot be negative.',
            'maximum_salary.numeric' => 'Maximum salary must be a number.',
            'maximum_salary.min' => 'Maximum salary cannot be negative.',
            'maximum_salary.gte' => 'Maximum salary must be greater than or equal to minimum salary.',
            'currency.required' => 'Currency is required.',
            'currency.size' => 'Currency must be a 3-letter code.',
            'required_by_date.required' => 'Required by date is required.',
            'required_by_date.date' => 'Required by date must be a valid date.',
            'proposed_start_date.date' => 'Proposed start date must be a valid date.',
            'urgency_level.required' => 'Urgency level is required.',
            'urgency_level.in' => 'Please select a valid urgency level.',
            'reason_for_requisition.required' => 'Reason for requisition is required.',
            'requisition_type.required' => 'Requisition type is required.',
            'requisition_type.in' => 'Please select a valid requisition type.',
            'replaced_employee_name.required' => 'Name of employee being replaced is required for replacement requisitions.',
            'replacement_reason.required' => 'Replacement reason is required for replacement requisitions.',
            'replacement_reason_other.required' => 'Please specify the other replacement reason.',
            'reporting_manager.required' => 'Reporting manager is required.',
            'reporting_manager.max' => 'Reporting manager name cannot exceed 100 characters.',
            'recruitment_source.required' => 'Recruitment source is required.',
            'recruitment_source.in' => 'Please select a valid recruitment source.',
        ];
    }
}
