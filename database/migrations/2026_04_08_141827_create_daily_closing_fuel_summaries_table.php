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
        Schema::create('daily_closing_fuel_summaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_closing_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('fuel_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('total_liters', 12, 2)->default(0);
            $table->decimal('returned_liters', 12, 2)->default(0);
            $table->decimal('net_liters', 12, 2)->default(0);

            $table->decimal('price_per_liter', 10, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);

            $table->timestamps();

            // يمنع تكرار نفس النوع داخل نفس الإغلاق
            $table->unique(['daily_closing_id', 'fuel_type_id'], 'dcfs_closing_fuel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_closing_fuel_summaries');
    }
};
