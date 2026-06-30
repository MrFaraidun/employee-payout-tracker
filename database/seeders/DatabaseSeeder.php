<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Payout;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Employees
        $employees = collect([
            'Aland Karwan',
            'Savan Azad',
            'Darya Rebwar',
            'Zana Ako',
            'Media Hassan',
        ])->map(function ($name) {
            return Employee::create(['name' => $name]);
        });

        // Seed Payouts
        Payout::create([
            'employee_id' => $employees->get(0)->id,
            'task' => 'Designed the layout interface',
            'amount_iqd' => 150000,
            'status' => 'completed',
        ]);

        Payout::create([
            'employee_id' => $employees->get(1)->id,
            'task' => 'Created database migrations and models',
            'amount_iqd' => 125000,
            'status' => 'completed',
        ]);

        Payout::create([
            'employee_id' => $employees->get(2)->id,
            'task' => 'Integrated API with frontend',
            'amount_iqd' => 175000,
            'status' => 'processing',
        ]);

        Payout::create([
            'employee_id' => $employees->get(3)->id,
            'task' => 'Wrote automated and manual tests',
            'amount_iqd' => 80000,
            'status' => 'pending',
        ]);
    }
}
