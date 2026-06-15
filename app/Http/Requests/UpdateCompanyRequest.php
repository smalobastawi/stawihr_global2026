<?php

namespace App\Http\Requests;

use App\Lib\Enumerations\Currency;
use App\Lib\Enumerations\PayrollCountry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
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
            'domain' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('companies', 'domain')->ignore($this->route('company')),
            ],
            'payroll_country' => ['required', 'integer', Rule::in(PayrollCountry::supportedIds())],
            'currency' => ['required', 'string', 'size:3', Rule::in(Currency::codes())],
            'status' => 'required',
            'kra_pin' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'nssf_employer_number' => 'nullable|string|max:50',
            'shif_employer_code' => 'nullable|string|max:50',
            'nita_registration_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:2000',
            'official_contact_number' => 'nullable|string|max:50',
            'official_email' => 'nullable|email|max:255',
            'company_contact_name' => 'nullable|string|max:255',
            'representative_phone' => 'nullable|string|max:50',
            'representative_email' => 'nullable|email|max:255',
            'print_head_description' => 'nullable|string|max:5000',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
        ];
    }
}