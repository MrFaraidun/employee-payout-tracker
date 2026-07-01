<?php

namespace Tests\Feature;

use App\Enums\EmployeeStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminA;
    protected User $adminB;
    protected Organization $orgA;
    protected Organization $orgB;
    protected Employee $employeeA;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRolesAndPermissions();

        $this->orgA = Organization::create(['name' => 'Org A']);
        $this->orgB = Organization::create(['name' => 'Org B']);

        $this->adminA = User::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Admin A',
            'email' => 'adminA@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::Admin,
            'email_verified_at' => now(),
        ]);
        $this->adminA->assignRole(UserRoleEnum::Admin->value);

        $this->adminB = User::create([
            'organization_id' => $this->orgB->id,
            'name' => 'Admin B',
            'email' => 'adminB@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::Admin,
            'email_verified_at' => now(),
        ]);
        $this->adminB->assignRole(UserRoleEnum::Admin->value);

        $this->employeeA = Employee::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Aland',
            'employee_code' => 'EMP-001',
            'department' => 'Engineering',
            'position' => 'Senior Developer',
        ]);
    }

    public function test_admin_can_view_own_organization_employees(): void
    {
        $response = $this->actingAs($this->adminA)->get('/admin/employees');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('employees.data', 1)
            ->where('employees.data.0.id', $this->employeeA->id)
        );
    }

    public function test_admin_cannot_view_other_organization_employees(): void
    {
        $response = $this->actingAs($this->adminB)->get('/admin/employees');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('employees.data', 0));
    }

    public function test_admin_can_onboard_employee(): void
    {
        $response = $this->actingAs($this->adminA)->post('/admin/employees', [
            'organization_id' => $this->orgA->id,
            'name' => 'New Employee',
            'employee_code' => 'EMP-999',
            'department' => 'Product',
            'position' => 'Designer',
            'status' => 'Active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', [
            'organization_id' => $this->orgA->id,
            'name' => 'New Employee',
            'employee_code' => 'EMP-999',
            'status' => EmployeeStatusEnum::Active->value,
        ]);
    }

    public function test_admin_can_archive_employee(): void
    {
        $response = $this->actingAs($this->adminA)->delete("/admin/employees/{$this->employeeA->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('employees', ['id' => $this->employeeA->id]);
    }
}
