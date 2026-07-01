<?php

namespace App\Policies;

use App\Models\Payout;
use App\Models\User;

class PayoutPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view payouts');
    }

    public function view(User $user, Payout $payout): bool
    {
        return $user->organization_id === $payout->organization_id && $user->can('view payouts');
    }

    public function create(User $user): bool
    {
        return $user->can('create payouts');
    }

    public function update(User $user, Payout $payout): bool
    {
        return $user->can('update payouts') && $user->organization_id === $payout->organization_id;
    }

    public function delete(User $user, Payout $payout): bool
    {
        return $user->can('delete payouts') && $user->organization_id === $payout->organization_id;
    }
}
