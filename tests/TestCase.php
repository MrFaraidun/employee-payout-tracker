<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    protected function setUpRolesAndPermissions(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => \App\Enums\UserRoleEnum::SuperAdmin->value, 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => \App\Enums\UserRoleEnum::Admin->value, 'guard_name' => 'web']);
        $accountantRole = Role::firstOrCreate(['name' => \App\Enums\UserRoleEnum::Accountant->value, 'guard_name' => 'web']);

        // Create Permissions
        $permissions = [
            'manage organizations',
            'manage admins',
            'manage employees',
            'manage payouts',
            'delete payouts',
            'view payouts',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign Permissions to Roles
        $adminRole->syncPermissions(['manage admins', 'manage employees', 'manage payouts', 'delete payouts', 'view payouts']);
        $accountantRole->syncPermissions(['view payouts', 'manage payouts']);
    }
}
