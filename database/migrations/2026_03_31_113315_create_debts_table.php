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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();

            $table->string('customer_name'); // اسم العميل
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->date('due_date')->nullable();
            $table->string('status')->default('pending'); // pending / paid / overdue

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
