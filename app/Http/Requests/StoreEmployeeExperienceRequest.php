<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeExperienceRequest extends FormRequest
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
            'organization_name' => ['required', 'string'],
            'designation' => ['required', 'string'],
            'from_date' => ['required'],
            'skill' => ['required', 'string'],
            'responsibility' => ['required', 'string']
        ];
    }
}
