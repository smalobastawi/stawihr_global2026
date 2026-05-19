<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobPostRequest extends FormRequest
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
        return [
            'job_title' => 'required|string|max:200',
            'job_description' => 'required|string',
            'job_type' => 'required|integer',
            'location_id' => 'required|integer',
            'status' => 'required|integer',
            'application_end_date' => 'required|date_format:d/m/Y',
            'job_publish_date' => 'required|date_format:d/m/Y',
            'audience_type' => 'required|in:internal,external,both',
            'employment_type' => 'nullable|string|in:full_time,part_time,contract,temporary,internship',
            'department_id' => 'nullable|integer',
            'number_of_positions' => 'nullable|integer|min:1',
            'minimum_salary' => 'nullable|numeric|min:0',
            'maximum_salary' => 'nullable|numeric|min:0|gte:minimum_salary',
            'job_requirements' => 'nullable|string',
            'minimum_qualifications' => 'nullable|string',
            'experience_required' => 'nullable|string|max:255',
            'job_requisition_id' => 'nullable|integer|exists:job_requisitions,job_requisition_id',
        ];
    }
}
