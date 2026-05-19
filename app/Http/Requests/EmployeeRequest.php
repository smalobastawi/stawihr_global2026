<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'KRA_Pin' => strtoupper($this->KRA_Pin),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $employeeId = $this->route('employee');
        $employee = \App\Models\Employee::find($employeeId);
        $userIdToIgnore = $employee ? $employee->user_id : null;

        $rules = [
            'user_name'             => 'nullable|unique:user,user_name,' . $userIdToIgnore . ',id',
            'first_name'        => 'required',
            'department_id'     => 'required',
            'designation_id'    => 'required',
            'work_shift_id'     => 'nullable',
            'email'             => 'required|unique:employee,email,' . ($this->route('employee') ? $this->route('employee') : 'NULL') . ',employee_id',
            'phone'             => 'required|numeric',
            'gender'            => 'nullable',
            'date_of_birth'     => 'required',
            'date_of_joining'   => 'required',
            'status'            => 'required',
            'institute.*'       => 'required',
            'board_university.*' => 'required',
            'degree.*'          => 'required',
            'passing_year.*'    => 'required',
            'designation.*'    => 'required',
            'organization_name.*' => 'required',
            'from_date.*'      => 'required',
            'to_date.*'        => 'required',
            'responsibility.*' => 'required',
            'skill.*'          => 'required',
            'photo'            => 'mimes:jpeg,jpg,png|max:1024',
            'payroll_number' => 'nullable|unique:employee,payroll_number,' . ($this->route('employee') ? $this->route('employee') : 'NULL') . ',employee_id',
            'personal_email' => 'nullable|email|unique:employee,personal_email,' . ($this->route('employee') ? $this->route('employee') : 'NULL') . ',employee_id',
            'daily_pay' => 'nullable',
            'NHIF_no' => 'nullable',
            'identity_type' => ['nullable', 'in:' . implode(',', array_keys(\App\Lib\Enumerations\IdentityType::toArray()))],

            'residential_status' => 'required'

        ];

        // Conditional rules for national_id based on identity_type
        $rules['national_id'] = [
            'nullable',
            // Unique rule for national_id
            $this->employee ? 'unique:employee,national_id,' . $this->employee . ',employee_id' : 'unique:employee',
            // Regex based on identity_type
            function ($attribute, $value, $fail) {
                $identityType = $this->input('identity_type');
                $regex = '';

                switch ($identityType) {
                    case \App\Lib\Enumerations\IdentityType::NATIONAL_ID:
                        $regex = '/.*/'; // No format check
                        break;
                    case \App\Lib\Enumerations\IdentityType::PASSPORT:
                        // General alphanumeric, no strict format
                        $regex = '/^[a-zA-Z0-9]+$/'; // Example: alphanumeric
                        break;
                    case \App\Lib\Enumerations\IdentityType::MILITARY_ID:
                        // No format check
                        $regex = '/.*/';
                        break;
                    case \App\Lib\Enumerations\IdentityType::DRIVING_LICENCE:
                        // Alphanumeric with hyphens
                        $regex = '/^[a-zA-Z0-9-]+$/'; // Example: alphanumeric with hyphens
                        break;
                    case \App\Lib\Enumerations\IdentityType::ALIEN_ID:
                        $regex = '/^\d{6,12}$/'; // 6 to 9 digits
                        break;
                    case \App\Lib\Enumerations\IdentityType::DIPLOMATIC_ID:
                        // General alphanumeric, no strict format
                        $regex = '/^[a-zA-Z0-9]+$/'; // Example: alphanumeric
                        break;
                    default:
                        // No specific identity type selected, apply a general rule or no rule
                        $regex = '/^[a-zA-Z0-9-\/]+$/'; // Allow general alphanumeric with common separators
                        break;
                }

                if (!empty($value) && !preg_match($regex, $value)) {
                    $fail('The ' . str_replace('_', ' ', $identityType) . ' format is invalid.');
                }
            },
        ];

        // KRA_Pin and NSSF_no rules are separate as they are not part of the 'national_id' field

        $rules['KRA_Pin'] = 'nullable';
        $rules['NSSF_no'] = ['required'];


        return $rules;
    }


    public function messages()
    {
        return [
            'role_id.required' => 'The role field is required.',
            'institute*.required' => 'The institute field is required.',
            'board_university*.required' => 'The board university field is required.',
            'degree*.required' => 'The degree field is required.',
            'passing_year*.required' => 'The passing year field is required.',
            'organization_name*.required' => 'The organization name field is required.',
            'from_date*.required' => 'The from date field is required.',
            'to_date*.required' => 'The to date field is required.',
            'supervisor_id*.required' => 'The supervisor field is required',
        ];
    }
}
