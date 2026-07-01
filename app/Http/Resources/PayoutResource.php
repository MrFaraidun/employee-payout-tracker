<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'task' => $this->task,
            'amount_iqd' => $this->amount_iqd,
            'status' => $this->status->value ?? $this->status,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'updated_by' => $this->updated_by,
            'updater' => new UserResource($this->whenLoaded('updater')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
