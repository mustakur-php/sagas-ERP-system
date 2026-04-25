<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_closing_expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_closing_id')
                ->constrained('daily_closings')
                ->cascadeOnDelete();

            $table->string('expense_type');
            // example: maintenance / salaries / transport / petty_cash / other

            $table->string('description')->nullable();

            $table->decimal('amount', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_closing_expenses');
    }
};