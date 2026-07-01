<?php

namespace App\Http\Requests;

use App\Enums\PayoutStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePayoutRequest extends FormRequest
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
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')->where(function ($query) {
                    $query->where('organization_id', $this->organization_id);
                })
            ],
            'task' => ['required', 'string', 'max:255'],
            'amount_iqd' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::enum(PayoutStatusEnum::class)],
        ];
    }
}
