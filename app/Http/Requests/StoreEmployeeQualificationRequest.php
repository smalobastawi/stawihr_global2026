<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeQualificationRequest extends FormRequest
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
            'institute' => ['required', 'string', 'max:255'],
            'board_university' => ['required', 'string'],
            'degree' => ['required', 'string'],
            'result' => ['required'],
            'cgpa' => ['required'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048']
        ];
    }
}
