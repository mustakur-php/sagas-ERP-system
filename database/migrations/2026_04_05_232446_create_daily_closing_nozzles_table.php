<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_closing_nozzles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_closing_id')
                ->constrained('daily_closings')
                ->cascadeOnDelete();

            $table->foreignId('station_nozzle_id')
                ->constrained('station_nozzles')
                ->cascadeOnDelete();

            $table->decimal('start_reading', 12, 2);
            $table->decimal('end_reading', 12, 2);
            $table->decimal('total_liters', 12, 2)->default(0);

            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(['daily_closing_id', 'station_nozzle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_closing_nozzles');
    }
};