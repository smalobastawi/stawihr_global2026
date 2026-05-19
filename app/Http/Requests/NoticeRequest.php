<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoticeRequest extends FormRequest
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
            'title'=>'required',
            'description'=>'required',
            'status'=>'required',
            'publish_date'=>'required',
            'attach_file'=>'mimes:jpeg,jpg,png,pdf|max:1024',
            'employees'=>'nullable|array',
            'employees.*'=>'exists:employee,employee_id',
            'send_sms'=>'nullable|in:1',
        ];
    }
}
