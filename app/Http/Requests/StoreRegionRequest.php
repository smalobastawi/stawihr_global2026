<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:regions,name',
            'manager_id' => 'nullable|exists:employee,employee_id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('region.name_required'),
            'name.unique' => __('region.name_unique')
        ];
    }
}
