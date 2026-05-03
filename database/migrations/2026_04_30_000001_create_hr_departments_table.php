<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('code')->nullable();
            $table->unsignedBigInteger('manager_employee_id')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'name_ar'], 'hr_departments_company_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_departments');
    }
};
