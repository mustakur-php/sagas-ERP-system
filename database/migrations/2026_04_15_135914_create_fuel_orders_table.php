<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_orders', function (Blueprint $table) {
            $table->id();

            // رقم الطلب
            $table->string('order_number')->unique();

            // مرتبط بالمحطة فقط (ومنها نعرف الشركة)
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();

            // من أنشأ الطلب
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // حالة الطلب (بسيطة)
            $table->string('status')->default('draft');
            // draft / submitted / in_progress / completed / cancelled

            // المرحلة الحالية (Workflow)
            $table->string('current_step')->nullable();
            // station_supervisor / operations_review / procurement / finance / transport / completed / cancelled

            // تاريخ الطلب
            $table->date('request_date')->nullable();

            // ملاحظات عامة
            $table->text('notes')->nullable();

            // تتبع الزمن
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            // Indexes مهمة
            $table->index('status');
            $table->index('current_step');
            $table->index('station_id');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_orders');
    }
};