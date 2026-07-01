<?php

namespace App\Listeners;

use App\Events\AdminCreated;
use App\Events\EmployeeCreated;
use App\Events\PayoutCreated;
use App\Services\AuditLogService;

class LogAction
{
    public function handle(mixed $event): void
    {
        if ($event instanceof AdminCreated) {
            AuditLogService::log(
                'user.created',
                $event->user,
                null,
                $event->user->toArray()
            );
        } elseif ($event instanceof EmployeeCreated) {
            AuditLogService::log(
                'employee.created',
                $event->employee,
                null,
                $event->employee->toArray()
            );
        } elseif ($event instanceof PayoutCreated) {
            AuditLogService::log(
                'payout.created',
                $event->payout,
                null,
                $event->payout->toArray()
            );
        }
    }
}
