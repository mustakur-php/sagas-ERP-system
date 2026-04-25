<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_closings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('station_id')
                ->constrained('stations')
                ->cascadeOnDelete();

            $table->date('closing_date');

            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('total_collections', 12, 2)->default(0);
            $table->decimal('total_expenses', 12, 2)->default(0);

            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected'])->default('draft');
            $table->index('status');
            
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['station_id', 'closing_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_closings');
    }
}; 