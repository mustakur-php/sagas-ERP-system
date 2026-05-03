<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('type')->index(); // hiring, leave, payroll, contract
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('group')->index(); // company_profile, work_schedule, leave_policy, payroll, permissions
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->string('value_type')->default('string');
            $table->timestamps();

            $table->unique(['company_id', 'group', 'key'], 'hr_settings_company_group_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_settings');
        Schema::dropIfExists('hr_activities');
    }
};
