<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDisciplinaryCaseRequest extends FormRequest
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
        return [
            //'case_number' => 'required|string|max:255|unique:disciplinary_cases,case_number',
            'category_id' => 'required|exists:disciplinary_categories,id',
            'location_id' => 'required|exists:location,location_id',
            'date_of_incident' => 'required|date',
            'location' => 'nullable|string|max:255',
            'employee_id' => 'required|exists:employee,employee_id',
            'reporter_id' => [
                'required',
                'exists:employee,employee_id',
                function ($attribute, $value, $fail) {
                    if ($value === $this->input('assigned_officer')) {
                        $fail('The Reporter and Assigned Officer cannot be the same person.');
                    }
                },
            ],
            'date_of_report' => 'nullable|date',
            'assigned_officer' => 'nullable|exists:employee,employee_id',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }
}
