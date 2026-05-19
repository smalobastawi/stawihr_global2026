<?php

namespace App\Http\Requests\Trainings;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilitatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Change this to 'true' to allow all users or implement your authorization logic
        return true; // Change to `false` only if you need specific authorization checks
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Facilitator Name
            'name' => ['required', 'string', 'max:255'],
            
            // Contact Email
            'contact_email' => ['nullable', 'email', 'max:255'],
            
            // Contact Phone
            'contact_phone' => ['nullable', 'string', 'max:20'],
            
            // Type (Internal or External)
            'type' => ['required', 'in:internal,external'],
            
            // Expertise
            'expertise' => ['nullable', 'string', 'max:500'],
            
            // Notes
            'notes' => ['nullable', 'string'],
          ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('training.facilitator_name')]),
            'name.string' => __('validation.string', ['attribute' => __('training.facilitator_name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('training.facilitator_name'), 'max' => 255]),
            
            'contact_email.email' => __('validation.email', ['attribute' => __('training.contact_email')]),
            'contact_email.max' => __('validation.max.string', ['attribute' => __('training.contact_email'), 'max' => 255]),
            
            'contact_phone.string' => __('validation.string', ['attribute' => __('training.contact_phone')]),
            'contact_phone.max' => __('validation.max.string', ['attribute' => __('training.contact_phone'), 'max' => 20]),
            
            'type.required' => __('validation.required', ['attribute' => __('training.type')]),
            'type.in' => __('validation.in', ['attribute' => __('training.type')]),
            
            'expertise.string' => __('validation.string', ['attribute' => __('training.expertise')]),
            'expertise.max' => __('validation.max.string', ['attribute' => __('training.expertise'), 'max' => 500]),
            
            'notes.string' => __('validation.string', ['attribute' => __('training.notes')]),
          ];
    }
}
