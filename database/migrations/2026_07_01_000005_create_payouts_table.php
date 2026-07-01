<?php

use App\Enums\PayoutStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('task');
            $table->unsignedBigInteger('amount_iqd');
            $table->string('status')->default(PayoutStatusEnum::Pending->value);
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
