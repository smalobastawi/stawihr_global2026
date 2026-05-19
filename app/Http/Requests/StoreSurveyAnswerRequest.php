<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyAnswerRequest extends FormRequest
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
            'survey_question_id' => ['required', 'integer', 'exists:survey_questions,id'],
            'answer_text' => ['required', 'array'],
            'answer_text.*' => ['required', 'string', 'max:255']
        ];
    }
}
