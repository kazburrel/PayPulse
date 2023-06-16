<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRegistrationRequest extends FormRequest
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
            'fname' => 'required',
            'lname' => 'required',
            'other_name' => 'nullable',
            'email' => ['required', 'email:filter', 'unique:' . User::class],
            'phone' => 'required',
            'dob' => 'required',
            'address' => 'required',
            'state' => 'required',
            'post_code' => 'required',
            'country' => 'required',
            'id_card' => 'required',
            'selfie' => 'required',
            'account_type' => 'required',
            'gender' => 'required',
            'employment_status' => 'required',
            't_c' => 'accepted',
            'security_question' => 'required',
            'username' => ['required', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => 'required',
        ];
    }
}
