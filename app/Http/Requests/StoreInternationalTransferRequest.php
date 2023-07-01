<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternationalTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'bank_name' => 'required',
            'bank_branch' => 'required',
            'country' => 'required',
            'email' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
            'beneficial_phone' => 'required',
            'swift_code' => 'required',
            'amount' => 'required',
            'description' => 'required',
        ];
    }
}
