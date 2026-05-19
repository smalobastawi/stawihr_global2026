<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InternalJobApplicationRequest extends FormRequest
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
            //
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255'
            ],
            'phone' => 'required|string|max:20',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string|min:50|max:2000',
            'years_of_experience' => 'required|integer|min:0|max:50',
            'highest_qualification' => 'required|in:None,High School,Associate Degree,Bachelor\'s Degree,Master\'s Degree,PhD',
            'location_id' => 'sometimes|required|exists:locations,location_id' // For internal applicants
        ];
    }
}
