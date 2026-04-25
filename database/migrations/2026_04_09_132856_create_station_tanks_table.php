<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_tanks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('station_id')
                ->constrained('stations')
                ->cascadeOnDelete();

            $table->foreignId('fuel_type_id')
                ->constrained('fuel_types')
                ->cascadeOnDelete();

            $table->string('name'); // مثال: الخزان 1
            $table->decimal('capacity', 12, 2); // السعة الكلية
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_quantity', 12, 2)->default(0); // الكمية الحالية

            $table->decimal('warning_level', 5, 2)->default(30); // نسبة التحذير %
            $table->decimal('critical_level', 5, 2)->default(10); // نسبة الخطر %

            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['station_id', 'fuel_type_id', 'name'],
                'st_tanks_station_fuel_name_unq'
            );

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_tanks');
    }
};