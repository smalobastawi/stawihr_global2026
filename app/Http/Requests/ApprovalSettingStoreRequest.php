<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalSettingStoreRequest extends FormRequest
{
    public function authorize()
    {
        // You can add authorization logic here if needed
        return true; // Allow all users to make this request
    }

    public function rules()
    {
        return [
            'number_of_approvers' => [
                'required', 
                'integer', 
                function ($attribute, $value, $fail) {
                    $approversCount = is_array($this->input('approvers')) ? count($this->input('approvers')) : 0;
                    if ((int) $value !== $approversCount) {
                        $fail('The number of approvers must match the approvers selected.');
                    }
                },
            ],
            'module_id' => [
                'required', 
                'string', 
                'unique:approval_settings,module_id', 

            ],
            'approvers' => [
                'required', 
                'array', 
            ],
        ];
    }

    public function messages()
    {
        return [
            'number_of_approvers.required' => 'The number of approvers is required.',
            'number_of_approvers.integer' => 'The number of approvers must be a valid number.',
            'module_id.required' => 'The model type is required.',
            'approvers.required' => 'Please select at least one approver.',
            'module_id.unique' => 'The department already exists. Please choose a different module.',
            'approvers.*.exists' => 'One or more selected approvers are invalid. Please select valid approvers.',
        ];
    }
}

