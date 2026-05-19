<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinancialYearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $financialYearId = $this->route('financial_year') ?? $this->route('id');

        return [
            'name' => 'required|string|max:255|unique:financial_years,name,' . $financialYearId,
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y|after_or_equal:start_date',
            'status' => 'required|in:1,2',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Year Name is required.',
            'name.unique' => 'This Year Name already exists. Please choose a different name.',
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
