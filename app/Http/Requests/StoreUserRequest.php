<?php

namespace App\Http\Requests;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => [
                'nullable',
                Rule::exists('organizations', 'id')
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'status' => ['required', Rule::enum(UserStatusEnum::class)],
        ];
    }
}
