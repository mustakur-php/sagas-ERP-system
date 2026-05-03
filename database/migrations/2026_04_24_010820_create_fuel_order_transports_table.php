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
        Schema::create('fuel_order_transports', function (Blueprint $table) {
            $table->id();

            // ربط مع الطلب
            $table->foreignId('fuel_order_id')->constrained()->cascadeOnDelete();

            // المورد
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();

            // الناقل
            $table->foreignId('carrier_id')->nullable()->constrained()->nullOnDelete();

            //تكلفه المورد مقابل الكمية المطلوبه 
            $table->decimal('supplier_total_cost', 12, 2)->nullable();

            // تكلفة النقل
            $table->decimal('transport_cost', 12, 2)->nullable();

            // بيانات الرحلة
            $table->string('driver_name')->nullable();
            $table->string('truck_number')->nullable();

            $table->timestamp('departure_time')->nullable();
            $table->timestamp('arrival_time')->nullable();

            // الحالة
            $table->enum('status', ['pending', 'assigned', 'in_transit', 'arrived', 'cancelled'])->default('pending');
            // pending / assigned / in_transit / arrived / cancelled

            // ملاحظات
            $table->text('notes')->nullable();

            // من عيّن النقل
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            $table->index('supplier_id');
            $table->index('carrier_id');
            $table->index('status');

            // كل طلب له نقل واحد فقط
            $table->unique('fuel_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_order_transports');
    }
};
