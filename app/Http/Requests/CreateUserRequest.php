<?php

namespace App\Http\Requests;

use App\Enums\UserRoles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', new Enum(UserRoles::class)],
        ];
    }
}
