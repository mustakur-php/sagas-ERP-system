<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fuel_order_id')
                ->constrained('fuel_orders')
                ->cascadeOnDelete();

            $table->foreignId('fuel_type_id')
                ->constrained('fuel_types')
                ->cascadeOnDelete();

            $table->decimal('requested_quantity', 12, 2)->default(0);
            $table->decimal('approved_quantity', 12, 2)->nullable();
            $table->decimal('received_quantity', 12, 2)->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'partially_received',
                'received',
                'rejected',
                'cancelled',
            ])->default('pending');

            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->unique(['fuel_order_id', 'fuel_type_id'], 'foi_order_fuel_unq');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_order_items');
    }
};