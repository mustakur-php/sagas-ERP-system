<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_nozzles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fuel_type_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('pump_number');
            $table->unsignedInteger('nozzle_number');

            $table->string('name')->nullable();

            $table->decimal('last_meter_reading', 12, 2)->default(0);
            $table->decimal('current_meter_reading', 12, 2)->default(0);

            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['station_id', 'pump_number', 'nozzle_number']);
            $table->index(['station_id', 'fuel_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_nozzles');
    }
};