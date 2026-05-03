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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->foreignId('station_id')->nullable()->constrained('stations')->nullOnDelete();

            $table->decimal('amount', 12, 2); // قيمة البيع
            $table->decimal('quantity', 12, 2)->nullable(); // اللترات (اختياري)
            $table->date('sale_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
