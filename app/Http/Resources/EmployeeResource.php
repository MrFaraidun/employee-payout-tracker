<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'name' => $this->name,
            'employee_code' => $this->employee_code,
            'department' => $this->department,
            'position' => $this->position,
            'status' => $this->status->value ?? $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
