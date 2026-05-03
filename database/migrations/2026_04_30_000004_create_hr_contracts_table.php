<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->enum('contract_type', ['full_time', 'part_time', 'temporary', 'probation'])->default('full_time');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('housing_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('other_allowances', 12, 2)->default(0);
            $table->enum('status', ['active', 'expired', 'renewed', 'terminated'])->default('active')->index();
            $table->date('renewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status'], 'hr_contracts_employee_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_contracts');
    }
};
