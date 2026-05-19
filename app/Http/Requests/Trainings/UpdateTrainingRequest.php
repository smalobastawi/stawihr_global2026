<?php

namespace App\Http\Requests\Trainings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingRequest extends FormRequest
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
        $training = $this->route('training');
        
        return [
            'training_type_id' => 'required|exists:training_type,training_type_id',
            'facilitator_id' => 'required|exists:training_facilitators,id',
            'subject' => 'required|string|max:255',
            'attendance_type' => 'required|in:physical,online',
            'attendance_link' => 'nullable|url|required_if:attendance_type,online',
            'attendance_location' => 'nullable|string|max:255|required_if:attendance_type,physical',
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($training) {
                    // If the date is being changed, it must be today or in the future
                    if ($value != $training->start_date->format('Y-m-d') && 
                        strtotime($value) < strtotime('today')) {
                        $fail('Start date must be today or in the future when changing the date');
                    }
                }
            ],
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    // If the dates are the same, end time must be after start time
                    if ($this->start_date == $this->end_date && 
                        strtotime($value) <= strtotime($this->start_time)) {
                        $fail('End time must be after start time when on the same day');
                    }
                }
            ],
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'attendance_link.required_if' => 'The online meeting link is required for online trainings',
            'attendance_location.required_if' => 'The physical location is required for in-person trainings',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }
}