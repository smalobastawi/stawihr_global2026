<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDisciplinaryCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'case_number' => 'nullable|string|max:255|unique:disciplinary_cases,case_number',
            'category_id' => 'required|exists:disciplinary_categories,id',
            'location_id' => 'required|exists:location,location_id',
            'date_of_incident' => 'required|date',
            'location' => 'nullable|string|max:255',
            'employee_id' => [
                'required',
                'exists:employee,employee_id',
                function ($attribute, $value, $fail) {
                    if ($value === $this->input('assigned_officer')) {
                        $fail('The employee and assigned officer cannot be the same person.');
                    }
                },
            ],
            'reporter_id' => [
                'required',
                'exists:employee,employee_id',
                function ($attribute, $value, $fail) {
                    if ($value === $this->input('assigned_officer')) {
                        $fail('The reporter and assigned officer cannot be the same person.');
                    }
                    if ($value === $this->input('employee_id')) {
                        $fail('The reporter and the employee cannot be the same person.');
                    }
                },
            ],
            'date_of_report' => 'nullable|date',
            'assigned_officer' => 'nullable|exists:employee,employee_id',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'case_number' => $this->case_number ?? $this->generateCaseNumber()
        ]);
    }

    /**
     * Generate a unique case number
     */
    protected function generateCaseNumber(): string
    {
        $prefix = 'DC-'; // Disciplinary Case prefix
        $year = date('Y');
        $month = date('m');
        $sequence = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}{$year}{$month}-{$sequence}";
    }
}