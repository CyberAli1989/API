<?php

namespace App\Http\Requests\Auth\Otp;

use Illuminate\Foundation\Http\FormRequest;

class OTPCodeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required' , 'string' , 'exists:otp_codes,code'],
            'login' => ['required' , 'string' , 'exists:users,email']
        ];
    }
}
