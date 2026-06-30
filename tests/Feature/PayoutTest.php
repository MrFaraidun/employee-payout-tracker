<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Payout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_payouts_page(): void
    {
        $response = $this->get('/payouts');

        $response->assertStatus(200);
    }

    public function test_can_create_payout_with_valid_data(): void
    {
        $employee = Employee::create(['name' => 'John Doe']);

        $response = $this->post('/payouts', [
            'employee_id' => $employee->id,
            'task' => 'Completed coding task',
            'amount_iqd' => 150000,
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payouts', [
            'employee_id' => $employee->id,
            'task' => 'Completed coding task',
            'amount_iqd' => 150000,
            'status' => 'completed',
        ]);
    }

    public function test_cannot_create_payout_with_invalid_data(): void
    {
        $response = $this->post('/payouts', [
            'employee_id' => 9999, // non-existent
            'task' => '', // required
            'amount_iqd' => -100, // min 1
            'status' => 'invalid-status', // allowed values: pending, processing, completed
        ]);

        $response->assertSessionHasErrors(['employee_id', 'task', 'amount_iqd', 'status']);
        $this->assertDatabaseEmpty('payouts');
    }

    public function test_can_update_full_fields_of_payout(): void
    {
        $employee1 = Employee::create(['name' => 'John Doe']);
        $employee2 = Employee::create(['name' => 'Jane Smith']);
        $payout = Payout::create([
            'employee_id' => $employee1->id,
            'task' => 'Original task',
            'amount_iqd' => 100000,
            'status' => 'pending',
        ]);

        $response = $this->put("/payouts/{$payout->id}", [
            'employee_id' => $employee2->id,
            'task' => 'Updated task description',
            'amount_iqd' => 250000,
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payouts', [
            'id' => $payout->id,
            'employee_id' => $employee2->id,
            'task' => 'Updated task description',
            'amount_iqd' => 250000,
            'status' => 'processing',
        ]);
    }

    public function test_can_update_status_of_payout(): void
    {
        $employee = Employee::create(['name' => 'John Doe']);
        $payout = Payout::create([
            'employee_id' => $employee->id,
            'task' => 'Quick status update task',
            'amount_iqd' => 100000,
            'status' => 'pending',
        ]);

        $response = $this->patch("/payouts/{$payout->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payouts', [
            'id' => $payout->id,
            'status' => 'completed',
        ]);
    }

    public function test_can_delete_payout(): void
    {
        $employee = Employee::create(['name' => 'John Doe']);
        $payout = Payout::create([
            'employee_id' => $employee->id,
            'task' => 'Task to be deleted',
            'amount_iqd' => 100000,
            'status' => 'pending',
        ]);

        $response = $this->delete("/payouts/{$payout->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('payouts', [
            'id' => $payout->id,
        ]);
    }
}
