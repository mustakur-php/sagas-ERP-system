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
        Schema::create('hr_employee_advances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('hr_employees')
                ->cascadeOnDelete();

            $table->decimal('amount', 12, 2);
            $table->date('advance_date');

            $table->enum('status', ['pending', 'approved', 'rejected', 'deducted'])
                ->default('pending')
                ->index();

            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('approved_at')->nullable();

            $table->text('reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_employee_advances');
    }
};
