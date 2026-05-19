<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUploadUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240',
            'category_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'approved_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'file.file' => 'The uploaded file is invalid.',
            'file.max' => 'The file size must not exceed 10MB.',
        ];
    }
}
