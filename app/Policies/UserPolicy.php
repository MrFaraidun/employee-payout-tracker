<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view admins');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('view admins') && ($user->role === \App\Enums\UserRoleEnum::SuperAdmin || $user->organization_id === $model->organization_id);
    }

    public function create(User $user): bool
    {
        return $user->can('create admins');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('update admins') && ($user->role === \App\Enums\UserRoleEnum::SuperAdmin || $user->organization_id === $model->organization_id);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('delete admins') && ($user->role === \App\Enums\UserRoleEnum::SuperAdmin || $user->organization_id === $model->organization_id) && $user->id !== $model->id;
    }
}
