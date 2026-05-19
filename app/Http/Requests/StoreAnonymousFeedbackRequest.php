<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnonymousFeedbackRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'=>'string|required|min:3',
            'content'=>'required|min:3',
            'category_id'=>'required|exists:feedback_categories,id'
        ];
    }
}
