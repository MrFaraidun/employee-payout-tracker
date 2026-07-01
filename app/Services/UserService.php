<?php

namespace App\Services;

use App\Events\AdminCreated;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'organization_id' => $data['organization_id'] ?? null,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'payout-tracker-default-123'),
                'role' => $data['role'],
                'status' => $data['status'] ?? \App\Enums\UserStatusEnum::Active->value,
            ]);

            // Assign role using Spatie
            $user->assignRole($data['role']->value ?? $data['role']);

            event(new AdminCreated($user));

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $oldValues = $user->toArray();
            
            $updateData = array_intersect_key($data, array_flip([
                'name', 'email', 'organization_id', 'status', 'role'
            ]));

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if (isset($data['role'])) {
                $user->syncRoles([$data['role']->value ?? $data['role']]);
            }

            $user->refresh();

            AuditLogService::log('user.updated', $user, $oldValues, $user->toArray());

            return $user;
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $oldValues = $user->toArray();
            $user->delete();

            AuditLogService::log('user.deleted', $user, $oldValues, null);
        });
    }
}
