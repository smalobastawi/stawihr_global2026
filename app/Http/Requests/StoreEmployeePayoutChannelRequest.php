<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeePayoutChannelRequest extends FormRequest
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
            'payout_channel_id' => 'required|integer|exists:payout_channels,id',
            'account_number' => 'required|string',
            'branch' => 'nullable|string',
            'branch_code' => 'nullable|string',
            'swift_code' => 'nullable|string',
        ];
    }
}