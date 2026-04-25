<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('station_tank_id')
                ->constrained('station_tanks')
                ->cascadeOnDelete();

            $table->foreignId('station_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('fuel_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('movement_type'); 
            // opening_balance / purchase / sale / returned / adjustment

            $table->decimal('quantity', 12, 2);
            // نخزنها موجبة دائمًا، ونفهم الاتجاه من نوع الحركة

            $table->date('movement_date');

            $table->string('reference_type')->nullable();
            // DailyClosing / FuelPurchase / ManualAdjustment ...

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['station_id', 'fuel_type_id', 'movement_date'], 'sm_station_fuel_date_idx');
            $table->index(['reference_type', 'reference_id'], 'sm_reference_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};