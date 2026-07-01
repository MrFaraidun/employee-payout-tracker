<?php

namespace Database\Seeders;

use App\Enums\EmployeeStatusEnum;
use App\Enums\OrganizationStatusEnum;
use App\Enums\PayoutStatusEnum;
use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            // Organizations
            'view organizations',
            'create organizations',
            'update organizations',
            'delete organizations',

            // Admins & Users
            'view admins',
            'create admins',
            'update admins',
            'delete admins',

            // Roles & Permissions
            'view roles',
            'create roles',
            'update roles',
            'delete roles',

            // Employees
            'view employees',
            'create employees',
            'update employees',
            'delete employees',

            // Payouts
            'view payouts',
            'create payouts',
            'update payouts',
            'delete payouts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Create Roles and Assign Permissions
        $superAdminRole = Role::firstOrCreate(['name' => UserRoleEnum::SuperAdmin->value, 'guard_name' => 'web']);
        // SuperAdmin gets all permissions
        $superAdminRole->syncPermissions($permissions);

        $adminRole = Role::firstOrCreate(['name' => UserRoleEnum::Admin->value, 'guard_name' => 'web']);
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

        $accountantRole = Role::firstOrCreate(['name' => UserRoleEnum::Accountant->value, 'guard_name' => 'web']);
        $accountantRole->syncPermissions([
            'view employees',
            'view payouts',
            'create payouts',
            'update payouts',
        ]);

        // 3. Create Organizations
        $acreTech = Organization::firstOrCreate(
            ['name' => 'Acre Tech'],
            ['status' => OrganizationStatusEnum::Active]
        );

        $basraPayout = Organization::firstOrCreate(
            ['name' => 'Basra Payout Corp'],
            ['status' => OrganizationStatusEnum::Active]
        );

        // 4. Create SuperAdmin (No Organization)
        $superAdmin = User::updateOrCreate(
            ['email' => 'super@example.com'],
            [
                'organization_id' => null,
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::SuperAdmin,
                'status' => UserStatusEnum::Active,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles([$superAdminRole]);

        // 5. Create Acre Tech Users
        $acreAdmin = User::updateOrCreate(
            ['email' => 'admin@acre.com'],
            [
                'organization_id' => $acreTech->id,
                'name' => 'Acre Admin',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::Admin,
                'status' => UserStatusEnum::Active,
                'email_verified_at' => now(),
            ]
        );
        $acreAdmin->syncRoles([$adminRole]);

        $acreAccountant = User::updateOrCreate(
            ['email' => 'accountant@acre.com'],
            [
                'organization_id' => $acreTech->id,
                'name' => 'Acre Accountant',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::Accountant,
                'status' => UserStatusEnum::Active,
                'email_verified_at' => now(),
            ]
        );
        $acreAccountant->syncRoles([$accountantRole]);

        // 6. Create Basra Users
        $basraAdmin = User::updateOrCreate(
            ['email' => 'admin@basra.com'],
            [
                'organization_id' => $basraPayout->id,
                'name' => 'Basra Admin',
                'password' => Hash::make('password'),
                'role' => UserRoleEnum::Admin,
                'status' => UserStatusEnum::Active,
                'email_verified_at' => now(),
            ]
        );
        $basraAdmin->syncRoles([$adminRole]);

        // 7. Seed Acre Tech Employees & Payouts
        $employeesData = [
            ['name' => 'Aland Karwan', 'code' => 'EMP-001', 'dept' => 'Engineering', 'pos' => 'Senior Developer'],
            ['name' => 'Savan Azad', 'code' => 'EMP-002', 'dept' => 'Engineering', 'pos' => 'Frontend Developer'],
            ['name' => 'Darya Rebwar', 'code' => 'EMP-003', 'dept' => 'Product', 'pos' => 'Product Designer'],
            ['name' => 'Zana Ako', 'code' => 'EMP-004', 'dept' => 'QA', 'pos' => 'QA Engineer'],
            ['name' => 'Media Hassan', 'code' => 'EMP-005', 'dept' => 'Marketing', 'pos' => 'Growth Lead'],
        ];

        foreach ($employeesData as $index => $emp) {
            $employee = Employee::firstOrCreate(
                ['employee_code' => $emp['code']],
                [
                    'organization_id' => $acreTech->id,
                    'name' => $emp['name'],
                    'department' => $emp['dept'],
                    'position' => $emp['pos'],
                    'status' => EmployeeStatusEnum::Active,
                ]
            );

            // Seed a Payout for each if not already seeded
            Payout::firstOrCreate(
                [
                    'organization_id' => $acreTech->id,
                    'employee_id' => $employee->id,
                    'task' => 'Monthly contract delivery for ' . $emp['name'],
                ],
                [
                    'amount_iqd' => 150000 + ($index * 12500),
                    'status' => $index % 2 === 0 ? PayoutStatusEnum::Completed : PayoutStatusEnum::Pending,
                    'paid_at' => $index % 2 === 0 ? now()->subDays(5) : null,
                    'created_by' => $acreAccountant->id,
                ]
            );
        }

        // 8. Seed Basra Employees & Payouts
        $basraEmployees = [
            ['name' => 'Ahmad Khalid', 'code' => 'EMP-901', 'dept' => 'Operations', 'pos' => 'Manager'],
            ['name' => 'Fatima Omar', 'code' => 'EMP-902', 'dept' => 'Support', 'pos' => 'Agent'],
        ];

        foreach ($basraEmployees as $index => $emp) {
            $employee = Employee::firstOrCreate(
                ['employee_code' => $emp['code']],
                [
                    'organization_id' => $basraPayout->id,
                    'name' => $emp['name'],
                    'department' => $emp['dept'],
                    'position' => $emp['pos'],
                    'status' => EmployeeStatusEnum::Active,
                ]
            );

            Payout::firstOrCreate(
                [
                    'organization_id' => $basraPayout->id,
                    'employee_id' => $employee->id,
                    'task' => 'Basra operations payout ' . $emp['name'],
                ],
                [
                    'amount_iqd' => 200000,
                    'status' => PayoutStatusEnum::Pending,
                    'created_by' => $basraAdmin->id,
                ]
            );
        }
    }
}
