<?php

namespace App\Http\Requests;

use App\Enums\OrganizationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized via policies
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('organizations', 'name')
            ],
            'status' => [
                'required',
                Rule::enum(OrganizationStatusEnum::class)
            ],
        ];
    }
}
