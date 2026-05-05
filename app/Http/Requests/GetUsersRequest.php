<?php

namespace App\Http\Requests;

use App\Enums\UserSort;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class GetUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sortBy' => ['nullable', new Enum(UserSort::class)],
        ];
    }
}
