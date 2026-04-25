<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_order_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fuel_order_id')
                ->constrained('fuel_orders')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action');
            // create / submit / approve / reject / return / assign_transport / confirm_payment / receive

            $table->string('from_step')->nullable();
            $table->string('to_step')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_order_logs');
    }
};