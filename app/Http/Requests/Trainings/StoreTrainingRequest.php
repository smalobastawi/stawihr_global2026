<?php

namespace App\Http\Requests\Trainings;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrainingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can add specific authorization logic here
        return true; // Allow all authorized users to create a training
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'training_type_id' => 'required|exists:training_type,training_type_id',
            'facilitator_id' => 'required|exists:training_facilitators,id',
            'subject' => 'required|string|max:255',
            'attendance_type' => 'required|in:physical,online,both',
            'attendance_link' => 'nullable|url',
            'attendance_location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date' => 'required|date|after_or_equal:start_date',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
        ];
    }
}
