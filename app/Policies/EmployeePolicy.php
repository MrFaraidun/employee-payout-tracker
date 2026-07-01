<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view employees') || $user->hasRole(\App\Enums\UserRoleEnum::Accountant->value);
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->organization_id === $employee->organization_id && $user->can('view employees');
    }

    public function create(User $user): bool
    {
        return $user->can('create employees');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->can('update employees') && $user->organization_id === $employee->organization_id;
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->can('delete employees') && $user->organization_id === $employee->organization_id;
    }
}
