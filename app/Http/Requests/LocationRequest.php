<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
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
        if (isset($this->location)) {
            return [
                'location_name'  => 'required|unique:location,location_name,' . $this->location . ',location_id',
                'region_id' => 'nullable',
            ];
        }
        return [
            'location_name' => 'required|unique:location',
            'region_id' => 'nullable',
        ];
    }
}
