<?php

namespace App\Http\Requests;

use App\Enums\EmployeeStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => [
                'required',
                Rule::exists('organizations', 'id')
            ],
            'name' => ['required', 'string', 'max:255'],
            'employee_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees', 'employee_code')
            ],
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(EmployeeStatusEnum::class)],
        ];
    }
}
