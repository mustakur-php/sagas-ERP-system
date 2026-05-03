<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('station_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreignId('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('hr_positions')->nullOnDelete();
            $table->unsignedBigInteger('work_location_id')->nullable();
            
            $table->string('employee_number')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('national_id')->nullable()->unique();
            $table->string('iqama_number')->nullable()->unique();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality')->nullable();
            $table->date('hire_date')->nullable();
            $table->enum('employment_status', ['active', 'on_leave', 'suspended', 'terminated'])->default('active')->index();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'employment_status'], 'hr_employees_company_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employees');
    }
};
