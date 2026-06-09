<?php

namespace App\Http\Requests;

use App\Support\CompanyContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinancialYearRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $companyId = $this->input('company_id') ?: CompanyContext::defaultCompanyIdForNewRecord();

        return [
            'company_id' => [
                Rule::requiredIf(fn () => CompanyContext::activeCompanyId() === null),
                'nullable',
                'integer',
                'exists:companies,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('financial_years', 'name')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
            'status' => 'required|in:1,2',
        ];
    }

    public function messages()
    {
        return [
            'company_id.required' => 'Company is required when no active company is selected.',
            'name.required' => 'Year Name is required.',
            'name.unique' => 'This year name already exists for the selected company.',
            'start_date.required' => 'Start Date is required.',
            'start_date.date_format' => 'Start Date must be in DD/MM/YYYY format.',
            'end_date.required' => 'End Date is required.',
            'end_date.date_format' => 'End Date must be in DD/MM/YYYY format.',
            'end_date.after_or_equal' => 'End Date must be after or equal to Start Date.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either Active or Inactive.',
        ];
    }
}
