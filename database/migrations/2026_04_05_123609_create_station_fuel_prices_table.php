<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_fuel_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('station_id')
                ->constrained('stations')
                ->cascadeOnDelete();

            $table->foreignId('fuel_type_id')
                ->constrained('fuel_types')
                ->cascadeOnDelete();

            $table->decimal('price_per_liter', 10, 2);

            $table->timestamps();

            $table->unique(['station_id', 'fuel_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_fuel_prices');
    }
};