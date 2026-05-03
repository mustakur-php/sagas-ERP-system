<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->decimal('total_basic_salary', 14, 2)->default(0);
            $table->decimal('total_allowances', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2)->default(0);
            $table->enum('status', ['draft', 'processing', 'completed', 'paid', 'cancelled'])->default('draft')->index();
            $table->unsignedBigInteger('processed_by')->nullable()->index();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'year', 'month'], 'hr_payroll_company_year_month_unique');
        });

        Schema::create('hr_payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('hr_payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('housing_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('other_allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->json('details')->nullable();
            $table->timestamps();

            $table->unique(['payroll_run_id', 'employee_id'], 'hr_payroll_items_run_employee_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_items');
        Schema::dropIfExists('hr_payroll_runs');
    }
};
