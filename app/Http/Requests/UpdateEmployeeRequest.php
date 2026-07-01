<?php

namespace App\Http\Requests;

use App\Enums\EmployeeStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee') ? (is_object($this->route('employee')) ? $this->route('employee')->id : $this->route('employee')) : null;

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
                Rule::unique('employees', 'employee_code')->ignore($employeeId)
            ],
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(EmployeeStatusEnum::class)],
        ];
    }
}
