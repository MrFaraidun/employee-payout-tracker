<?php

namespace Tests\Feature;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminA;
    protected Organization $orgA;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRolesAndPermissions();

        $this->orgA = Organization::create(['name' => 'Org A']);

        $this->superAdmin = User::create([
            'organization_id' => null,
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::SuperAdmin,
            'email_verified_at' => now(),
        ]);
        $this->superAdmin->assignRole(UserRoleEnum::SuperAdmin->value);

        $this->adminA = User::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Admin A',
            'email' => 'adminA@example.com',
            'password' => Hash::make('password'),
            'role' => UserRoleEnum::Admin,
            'email_verified_at' => now(),
        ]);
        $this->adminA->assignRole(UserRoleEnum::Admin->value);
    }

    public function test_super_admin_can_view_all_users(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('users.data', 2)); // superAdmin + adminA
    }

    public function test_admin_can_only_view_own_organization_users(): void
    {
        $response = $this->actingAs($this->adminA)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.id', $this->adminA->id)
        );
    }

    public function test_super_admin_can_create_admin(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/admin/users', [
            'organization_id' => $this->orgA->id,
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'password' => 'password123',
            'role' => 'Admin',
            'status' => 'Active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'role' => UserRoleEnum::Admin->value,
        ]);
    }

    public function test_admin_cannot_create_super_admin(): void
    {
        // If Admin tries to send role 'SuperAdmin', it is overridden to Accountant
        $response = $this->actingAs($this->adminA)->post('/admin/users', [
            'organization_id' => $this->orgA->id,
            'name' => 'Malicious Admin',
            'email' => 'malicious@example.com',
            'password' => 'password123',
            'role' => 'SuperAdmin',
            'status' => 'Active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Malicious Admin',
            'role' => UserRoleEnum::Accountant->value, // downgraded
        ]);
    }
}
