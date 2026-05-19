<?php

namespace App\Http\Requests\Trainings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateFacilitatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Ensure the user is authorized to update the facilitator.
        // Set to true if authorization is not needed, or implement any additional logic.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255', // Facilitator's name is required and should be a string.
            'contact_email' => 'nullable|email|max:255', // Contact email is optional but should be a valid email.
            'contact_phone' => 'nullable|string|max:25', // Contact phone is optional, but should be a string.
            'type' => 'required|in:internal,external', // Type must be either 'internal' or 'external'.
            'expertise' => 'required|string|max:255', // Expertise area is required and should be a string.
            'notes' => 'nullable|string', // Notes are optional but should be a string.
          ];
    }

    /**
     * Get custom attributes for the validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'name' => __('training.facilitator_name'),
            'contact_email' => __('training.contact_email'),
            'contact_phone' => __('training.contact_phone'),
            'type' => __('training.type'),
            'expertise' => __('training.expertise'),
            'notes' => __('training.notes'), 
        ];
    }

    /** 
     * Customize the response for failed validation (optional).
     */
    protected function failedValidation(Validator $validator)
    {
        // Custom response if validation fails (optional).
        // For instance, you can return a different message or log failed validation attempts.
        parent::failedValidation($validator);
    }
}
