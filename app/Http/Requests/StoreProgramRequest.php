<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'main_program' => 'nullable|exists:programs,id|different:id', // Prevent self-referencing
             
            'status' => 'required|in:active,inactive,completed',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->main_program && $this->isCircular($this->main_program)) {
                $validator->errors()->add('main_program', 'The selected parent program would create a circular relationship.');
            }
        });
    }

    /**
     * Check for circular relationship.
     *
     * @param  int  $mainProgramId
     * @return bool
     */
    protected function isCircular($mainProgramId)
    {
        $parentId = $mainProgramId;

        while ($parentId) {
            if ($parentId == $this->id) {
                return true; // Circular relationship detected
            }
            $parentId = \App\Models\Program::find($parentId)?->main_program;
        }

        return false;
    }
}
