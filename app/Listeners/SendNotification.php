<?php

namespace App\Listeners;

use App\Events\AdminCreated;
use App\Events\PayoutCreated;
use App\Models\User;
use App\Notifications\AdminInvitedNotification;
use App\Notifications\PayoutCreatedNotification;
use Illuminate\Support\Facades\Notification;

class SendNotification
{
    public function handle(mixed $event): void
    {
        if ($event instanceof AdminCreated) {
            $event->user->notify(new AdminInvitedNotification($event->user));
        } elseif ($event instanceof PayoutCreated) {
            $payout = $event->payout;
            $organizationId = $payout->organization_id;

            // Fetch all admins in the same organization to notify
            $admins = User::where('organization_id', $organizationId)
                ->where('role', \App\Enums\UserRoleEnum::Admin)
                ->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new PayoutCreatedNotification($payout));
            }
        }
    }
}
