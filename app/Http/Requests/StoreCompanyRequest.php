<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:companies,domain',
            'country' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'kra_pin' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'nssf_employer_number' => 'nullable|string|max:50',
            'shif_employer_code' => 'nullable|string|max:50',
            'employer_number' => 'nullable|string|max:50',
            'nita_registration_number' => 'nullable|string|max:50',
            'ecitizen_identifier' => 'nullable|string|max:50',
        ];
    }
}
