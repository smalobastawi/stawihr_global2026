<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
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
            'holiday_name' => 'required',
        ];

        if(isset($this->manageHoliday)){
            $rules['holiday_name'] = 'required|unique:holiday,holiday_name,'.$this->manageHoliday.',holiday_id';
        } else {
            $rules['holiday_name'] = 'required|unique:holiday';
        }

        return $rules;
    }
}
