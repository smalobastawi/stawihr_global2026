<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
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
            'section_head_id' => 'nullable|exists:employee,employee_id',
            'location_id' => 'nullable|exists:location,location_id',
        ];

        if (isset($this->employeeSection)) {
            $rules['name'] = 'required|unique:employee_sections,name,' . $this->employeeSection;
        } else {
            $rules['name'] = 'required|unique:employee_sections';
        }

        return $rules;
    }
}
