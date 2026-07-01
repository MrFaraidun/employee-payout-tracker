<?php

namespace App\Policies;

use App\Models\Payout;
use App\Models\User;

class PayoutPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view payouts') || $user->can('manage payouts');
    }

    public function view(User $user, Payout $payout): bool
    {
        return $user->organization_id === $payout->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->can('manage payouts');
    }

    public function update(User $user, Payout $payout): bool
    {
        return $user->can('manage payouts') && $user->organization_id === $payout->organization_id;
    }

    public function delete(User $user, Payout $payout): bool
    {
        return $user->can('delete payouts') && $user->organization_id === $payout->organization_id;
    }
}
