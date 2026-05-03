<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->date('attendance_date')->index();
            $table->time('check_in')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->integer('check_in_distance_meters')->nullable();

            $table->time('check_out')->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->integer('check_out_distance_meters')->nullable();
            $table->decimal('worked_hours', 5, 2)->default(0);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_leave_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->enum('status', ['present', 'absent', 'late', 'on_leave', 'excused'])->default('present')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date'], 'hr_attendance_employee_date_unique');
            $table->index(['attendance_date', 'status'], 'hr_attendance_date_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_attendance_records');
    }
};
