<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->foreignId('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->string('code')->nullable();
            $table->decimal('default_basic_salary', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'department_id', 'title_ar'], 'hr_positions_company_dept_title_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_positions');
    }
};
