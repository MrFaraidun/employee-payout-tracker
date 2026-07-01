<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(\App\Enums\UserRoleEnum::SuperAdmin->value) || $user->can('view organizations');
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->organization_id === $organization->id && $user->can('view organizations');
    }

    public function create(User $user): bool
    {
        return false; // SuperAdmin only (handled by Gate::before)
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->organization_id === $organization->id && $user->can('update organizations');
    }

    public function delete(User $user, Organization $organization): bool
    {
        return false; // SuperAdmin only
    }
}
