<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class OrganizationService
{
    public function create(array $data): Organization
    {
        return DB::transaction(function () use ($data) {
            $organization = Organization::create($data);

            AuditLogService::log('organization.created', $organization, null, $organization->toArray());

            return $organization;
        });
    }

    public function update(Organization $organization, array $data): Organization
    {
        return DB::transaction(function () use ($organization, $data) {
            $oldValues = $organization->toArray();
            $organization->update($data);
            $organization->refresh();

            AuditLogService::log('organization.updated', $organization, $oldValues, $organization->toArray());

            return $organization;
        });
    }

    public function delete(Organization $organization): void
    {
        DB::transaction(function () use ($organization) {
            $oldValues = $organization->toArray();
            $organization->delete();

            AuditLogService::log('organization.deleted', $organization, $oldValues, null);
        });
    }
}
