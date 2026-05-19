<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApprovalDelegationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'delegate_to_user_id' => 'required|exists:user,id',
            'model_type' => 'nullable|string',
            'delegation_type' => 'required|in:all,specific_model,specific_workflow',
            'workflow_id' => 'nullable|exists:approval_workflows,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'include_submissions' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ];
    }
}

class UpdateApprovalDelegationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'model_type' => 'nullable|string',
            'delegation_type' => 'required|in:all,specific_model,specific_workflow',
            'workflow_id' => 'nullable|exists:approval_workflows,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
            'include_submissions' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
