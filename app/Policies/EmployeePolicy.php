<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage employees') || $user->hasRole(\App\Enums\UserRoleEnum::Accountant->value);
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->organization_id === $employee->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->can('manage employees');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->can('manage employees') && $user->organization_id === $employee->organization_id;
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->can('manage employees') && $user->organization_id === $employee->organization_id;
    }
}
