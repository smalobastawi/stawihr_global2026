<?php

namespace App\Http\Requests;

use App\Lib\Enumerations\Currency;
use App\Lib\Enumerations\ExchangeRateEffectiveDatePolicy;
use App\Lib\Enumerations\ExchangeRateSource;
use App\Lib\Enumerations\PayrollCountry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'domain' => 'nullable|string|max:255|unique:companies,domain',
            'payroll_country' => ['required', 'integer', Rule::in(PayrollCountry::supportedIds())],
            'currency' => ['required', 'string', 'size:3', Rule::in(Currency::codes())],
            'payroll_base_currency' => ['nullable', 'string', 'size:3', Rule::in(Currency::codes())],
            'default_payment_currency' => ['nullable', 'string', 'size:3', Rule::in(Currency::codes())],
            'exchange_rate_source' => ['nullable', Rule::in(array_keys(ExchangeRateSource::toArray()))],
            'exchange_rate_effective_date_policy' => ['nullable', Rule::in(array_keys(ExchangeRateEffectiveDatePolicy::toArray()))],
            'allow_employee_payment_currency' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
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
