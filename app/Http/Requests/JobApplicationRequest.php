<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class JobApplicationRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'job_id' => 'required|exists:job,job_id',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check if email exists in User or Employee tables
                    if (
                        User::where('email', $value)->exists() ||
                        Employee::where('email', $value)->exists()
                    ) {
                        $fail('This email is already associated with an existing employee or user account.');
                    }
                },
            ],
            'phone' => 'required|string|max:20',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string|min:50|max:2000',
            'years_of_experience' => 'required|integer|min:0|max:50',
            'highest_qualification' => 'required|in:None,High School,Associate Degree,Bachelor\'s Degree,Master\'s Degree,PhD',
            'location_id' => 'sometimes|required|exists:locations,location_id', // For internal applicants

            // New fields - all optional
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Male,Female,Other,Prefer not to say',
            'nationality' => 'nullable|string|max:100',
            'current_address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'current_employer' => 'nullable|string|max:255',
            'current_position' => 'nullable|string|max:255',
            'notice_period' => 'nullable|in:Immediate,1 week,2 weeks,1 month,2 months,3 months,More than 3 months',
            'expected_salary' => 'nullable|numeric|min:0',
            'linkedin_url' => 'nullable|url|max:500',
            'portfolio_url' => 'nullable|url|max:500',
            'referral_source' => 'nullable|in:Company Website,LinkedIn,Job Board,Social Media,Referral,Recruitment Agency,Other',
            'additional_comments' => 'nullable|string|max:2000',
            'consent' => 'required|accepted',
        ];
    }

    public function messages()
    {
        return [
            'cover_letter.min' => 'The cover letter should be at least 50 characters',
            'resume.mimes' => 'Only PDF, DOC, and DOCX files are allowed',
            'highest_qualification.in' => 'Please select a valid qualification',
            'linkedin_url.url' => 'Please enter a valid LinkedIn URL (e.g., https://linkedin.com/in/yourprofile)',
            'portfolio_url.url' => 'Please enter a valid URL (e.g., https://yourportfolio.com)',
            'consent.accepted' => 'You must confirm the information accuracy to proceed',
            'date_of_birth.before' => 'Date of birth must be in the past',
        ];
    }
}
