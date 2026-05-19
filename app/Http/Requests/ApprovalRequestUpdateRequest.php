<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequestUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason' => 'required|string|max:255', // Ensure 'reason' is a required string with a maximum length of 255
            'status' => 'required', // Ensure 'status' is provided
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'reason.required' => 'The reason field is required.',
            'reason.string' => 'The reason must be a valid string.',
            'reason.max' => 'The reason may not exceed 255 characters.',
            'status.required' => 'The status field is required.',
        ];
    }
}
