<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalSettingUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // You can add authorization logic here if needed
        return true; // Allow all users to make this request
    }

    public function rules()
    {
        return [
            'module_id' => 'required|exists:modules,id',
            'number_of_approvers' => 'required|integer|min:1',
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'exists:employee,employee_id',
        ];
    }

    public function messages()
    {
        return [
            'module_id.required' => 'The model type is required.',
            'module_id.exists' => 'The selected model type is invalid.',
            'number_of_approvers.required' => 'The number of approvers is required.',
            'number_of_approvers.integer' => 'The number of approvers must be a valid integer.',
            'number_of_approvers.min' => 'The number of approvers must be at least 1.',
            'approvers.required' => 'Please select at least one approver.',
            'approvers.array' => 'The approvers must be an array.',
            'approvers.min' => 'You must select at least one approver.',
            'approvers.*.exists' => 'One or more selected approvers are invalid. Please select valid approvers.',
            'module_id.unique' => 'The department already exists. Please choose a different module.',

        ];
    }

    protected function prepareForValidation()
    {
        // You can manipulate the request data before validation if needed
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        parent::failedValidation($validator);
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (count($this->input('approvers', [])) !== (int)$this->input('number_of_approvers')) {
                $validator->errors()->add(
                    'approvers',
                    'The number of approvers must match the count of selected approvers.'
                );
            }
        });
    }
}

