<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update with your authorization logic if needed
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions', 'name')->ignore($this->route('region')),
            ],
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