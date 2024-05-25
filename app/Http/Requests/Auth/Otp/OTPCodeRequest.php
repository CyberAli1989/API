<?php

namespace App\Http\Requests\Auth\Otp;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class OTPCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        if (Validator::make($this->all(), [
            'login' => ['required', 'string', 'email']
        ])->fails())
            $this->merge([
                'type' => User::PHONE_Type,
            ]);
        else
            $this->merge([
                'type' => User::EMAIL_Type,
            ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'exists:otp_codes,code'],
            'login' => ['required', 'string', $this['type'] === User::EMAIL_Type ? 'exists:users,email' : 'exists:users,phone']
        ];
    }
}
