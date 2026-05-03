<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hr_payroll_policies', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id')->nullable()->index();

            // أوقات العمل
            $table->decimal('standard_daily_hours', 5, 2)->default(8);
            $table->unsignedInteger('monthly_working_days')->default(30);

            // التأخير
            $table->unsignedInteger('late_grace_minutes')->default(15);
            $table->decimal('late_deduction_per_minute', 8, 2)->default(0);

            // الخروج المبكر
            $table->decimal('early_leave_deduction_per_minute', 8, 2)->default(0);

            // الإضافي
            $table->boolean('overtime_enabled')->default(true);
            $table->decimal('overtime_rate_multiplier', 5, 2)->default(1.5);

            // الغياب
            $table->enum('absence_deduction_type', ['full_day', 'none'])->default('full_day');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_policies');
    }
};
