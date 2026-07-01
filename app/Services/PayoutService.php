<?php

namespace App\Services;

use App\Events\PayoutCreated;
use App\Models\Payout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    public function create(array $data): Payout
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = $data['created_by'] ?? Auth::id();
            
            if (isset($data['status']) && $data['status'] === \App\Enums\PayoutStatusEnum::Completed->value) {
                $data['paid_at'] = $data['paid_at'] ?? now();
            }

            $payout = Payout::create($data);

            event(new PayoutCreated($payout));

            return $payout;
        });
    }

    public function update(Payout $payout, array $data): Payout
    {
        return DB::transaction(function () use ($payout, $data) {
            $oldValues = $payout->toArray();
            
            $data['updated_by'] = Auth::id();

            if (isset($data['status'])) {
                if ($data['status'] === \App\Enums\PayoutStatusEnum::Completed->value) {
                    $data['paid_at'] = $data['paid_at'] ?? now();
                } else {
                    $data['paid_at'] = null;
                }
            }

            $payout->update($data);
            $payout->refresh();

            AuditLogService::log('payout.updated', $payout, $oldValues, $payout->toArray());

            return $payout;
        });
    }

    public function delete(Payout $payout): void
    {
        DB::transaction(function () use ($payout) {
            $oldValues = $payout->toArray();
            $payout->delete();

            AuditLogService::log('payout.deleted', $payout, $oldValues, null);
        });
    }
}
