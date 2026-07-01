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
            'view organizations',
            'create organizations',
            'update organizations',
            'delete organizations',
            'view admins',
            'create admins',
            'update admins',
            'delete admins',
            'view roles',
            'create roles',
            'update roles',
            'delete roles',
            'view employees',
            'create employees',
            'update employees',
            'delete employees',
            'view payouts',
            'create payouts',
            'update payouts',
            'delete payouts',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign Permissions to Roles
        $adminRole->syncPermissions([
            'view organizations',
            'update organizations',
            'view admins',
            'create admins',
            'update admins',
            'delete admins',
            'view roles',
            'create roles',
            'update roles',
            'delete roles',
            'view employees',
            'create employees',
            'update employees',
            'delete employees',
            'view payouts',
            'create payouts',
            'update payouts',
            'delete payouts',
        ]);
        $accountantRole->syncPermissions([
            'view employees',
            'view payouts',
            'create payouts',
            'update payouts',
        ]);
    }
}
