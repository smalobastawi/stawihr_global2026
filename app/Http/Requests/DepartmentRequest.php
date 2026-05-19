<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'department_head_id' => 'nullable|exists:employee,employee_id'
        ];

        if(isset($this->department)){
            $rules['department_name'] = 'required|unique:department,department_name,'.$this->department.',department_id';
        } else {
            $rules['department_name'] = 'required|unique:department';
        }

        return $rules;
    }
}
