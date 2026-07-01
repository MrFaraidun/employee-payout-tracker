<?php

namespace Tests\Feature;

use App\Enums\PayoutStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminA;
    protected User $accountantA;
    protected User $adminB;
    protected Organization $orgA;
    protected Organization $orgB;
    protected Employee $employeeA;
    protected Employee $employeeB;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->setUpRolesAndPermissions();

        // Organizations
        $this->orgA = Organization::create(['name' => 'Org A']);
        $this->orgB = Organization::create(['name' => 'Org B']);

        // Users
        $this->adminA = User::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Admin A',
            'email' => 'adminA@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::Admin,
            'email_verified_at' => now(),
        ]);
        $this->adminA->assignRole(UserRoleEnum::Admin->value);

        $this->accountantA = User::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Accountant A',
            'email' => 'accountantA@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::Accountant,
            'email_verified_at' => now(),
        ]);
        $this->accountantA->assignRole(UserRoleEnum::Accountant->value);

        $this->adminB = User::create([
            'organization_id' => $this->orgB->id,
            'name' => 'Admin B',
            'email' => 'adminB@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::Admin,
            'email_verified_at' => now(),
        ]);
        $this->adminB->assignRole(UserRoleEnum::Admin->value);

        // Employees
        $this->employeeA = Employee::create([
            'organization_id' => $this->orgA->id,
            'name' => 'John Doe',
            'employee_code' => 'EMP-001',
            'department' => 'Engineering',
            'position' => 'Senior Dev',
        ]);

        $this->employeeB = Employee::create([
            'organization_id' => $this->orgB->id,
            'name' => 'Jane Smith',
            'employee_code' => 'EMP-002',
            'department' => 'Product',
            'position' => 'Designer',
        ]);
    }

    public function test_guest_cannot_view_payouts_page(): void
    {
        $response = $this->get('/admin/payouts');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_own_organization_payouts(): void
    {
        $payout = Payout::create([
            'organization_id' => $this->orgA->id,
            'employee_id' => $this->employeeA->id,
            'task' => 'Contract task',
            'amount_iqd' => 150000,
            'status' => PayoutStatusEnum::Pending,
            'created_by' => $this->adminA->id,
        ]);

        $response = $this->actingAs($this->adminA)->get('/admin/payouts');
        
        $response->assertStatus(200);
        // Ensure payout is rendered (check Inertia properties)
        $response->assertInertia(fn ($page) => $page
            ->has('payouts.data', 1)
            ->where('payouts.data.0.id', $payout->id)
        );
    }

    public function test_admin_cannot_view_other_organization_payouts(): void
    {
        // Payout of Org B
        $payoutB = Payout::create([
            'organization_id' => $this->orgB->id,
            'employee_id' => $this->employeeB->id,
            'task' => 'Org B Payout',
            'amount_iqd' => 100000,
            'status' => PayoutStatusEnum::Completed,
            'created_by' => $this->adminB->id,
        ]);

        $response = $this->actingAs($this->adminA)->get('/admin/payouts');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('payouts.data', 0));
    }

    public function test_accountant_can_create_payout_in_own_organization(): void
    {
        $response = $this->actingAs($this->accountantA)->post('/admin/payouts', [
            'organization_id' => $this->orgA->id,
            'employee_id' => $this->employeeA->id,
            'task' => 'Code submission',
            'amount_iqd' => 120000,
            'status' => 'Pending',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payouts', [
            'organization_id' => $this->orgA->id,
            'employee_id' => $this->employeeA->id,
            'task' => 'Code submission',
            'amount_iqd' => 120000,
            'status' => PayoutStatusEnum::Pending->value,
            'created_by' => $this->accountantA->id,
        ]);
    }

    public function test_accountant_cannot_delete_payout(): void
    {
        $payout = Payout::create([
            'organization_id' => $this->orgA->id,
            'employee_id' => $this->employeeA->id,
            'task' => 'Contract task',
            'amount_iqd' => 150000,
            'status' => PayoutStatusEnum::Pending,
            'created_by' => $this->adminA->id,
        ]);

        $response = $this->actingAs($this->accountantA)->delete("/admin/payouts/{$payout->id}");
        
        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseHas('payouts', ['id' => $payout->id]);
    }

    public function test_admin_can_delete_payout(): void
    {
        $payout = Payout::create([
            'organization_id' => $this->orgA->id,
            'employee_id' => $this->employeeA->id,
            'task' => 'Contract task',
            'amount_iqd' => 150000,
            'status' => PayoutStatusEnum::Pending,
            'created_by' => $this->adminA->id,
        ]);

        $response = $this->actingAs($this->adminA)->delete("/admin/payouts/{$payout->id}");
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('payouts', ['id' => $payout->id]);
    }
}
