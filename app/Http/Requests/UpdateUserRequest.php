<?php

namespace App\Http\Requests;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user') ? (is_object($this->route('user')) ? $this->route('user')->id : $this->route('user')) : null;

        return [
            'organization_id' => [
                'nullable',
                Rule::exists('organizations', 'id')
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => ['nullable', Password::defaults()],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'status' => ['required', Rule::enum(UserStatusEnum::class)],
        ];
    }
}
