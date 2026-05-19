<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class AddRolloverLeaveRequest extends FormRequest
{
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
           // 'employee'=>'required|unique:leave_rollovers,employee_id,'.$this->employee_id.',employee_id',
           // 'employee'=>'required',
            'no_of_days'=>'required',
            'leave_type'=>'required',
            'fiscal_year'=>'required',
        ];
    }

    public function messages()
    {

        return [
            'employee.required' => 'The employee field is required.',
            'no_of_days.required' => 'The number of days field is required.',
        ];
    }

}
