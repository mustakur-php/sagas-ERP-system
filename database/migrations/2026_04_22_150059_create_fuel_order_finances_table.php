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
        Schema::create('fuel_order_finances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fuel_order_id')->constrained()->cascadeOnDelete();

            $table->string('status')->default('pending');
            // pending / approved / rejected / paid

            $table->decimal('amount', 12, 2)->nullable();

            $table->string('payment_reference')->nullable();
            $table->string('bank_name')->nullable();

            $table->unique(
                ['payment_reference', 'bank_name'],
                'fuel_finance_payment_reference_bank_unique'
            );

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_order_finances');
    }
};
