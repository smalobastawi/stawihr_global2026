<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDisciplinaryCategoryRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'name'=>'string|required|min:3|unique:disciplinary_categories,name',
            'description'=>'min:3',
            'status'=>'required'
        ];
    }
}
