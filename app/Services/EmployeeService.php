<?php

namespace App\Services;

use App\Events\EmployeeCreated;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function create(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $employee = Employee::create($data);

            event(new EmployeeCreated($employee));

            return $employee;
        });
    }

    public function update(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $oldValues = $employee->toArray();
            $employee->update($data);
            $employee->refresh();

            AuditLogService::log('employee.updated', $employee, $oldValues, $employee->toArray());

            return $employee;
        });
    }

    public function delete(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            $oldValues = $employee->toArray();
            $employee->delete();

            AuditLogService::log('employee.deleted', $employee, $oldValues, null);
        });
    }
}
