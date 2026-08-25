<?php

namespace App\Http\Requests\Store\Auth;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Customer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterCustomerRequest extends FormRequest
{
    use PasswordValidationRules, ProfileValidationRules;

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            ...$this->profileRules(Customer::class, input: $this->all()),
            'password' => $this->passwordRules(),
        ];
    }
}
