<?php

use App\Enums\EmployeeStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('employee_code')->unique();
            $table->string('department');
            $table->string('position');
            $table->string('status')->default(EmployeeStatusEnum::Active->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
