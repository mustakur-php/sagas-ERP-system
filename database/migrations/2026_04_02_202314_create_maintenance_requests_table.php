<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();

            $table->string('report_number')->unique();


            $table->foreignId('station_id')
                ->nullable()
                ->constrained('stations')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('priority', ['low','medium','high','urgent'])->default('medium');

            $table->enum('status', [
                'new',
                'under_review',
                'assigned_to_department',
                'assigned_to_technician',
                'in_progress',
                'pending_operations_approval',
                'returned',
                'closed',
                'cancelled'
            ])->default('new');

            $table->enum('current_step', [
                'operations_review',
                'department_review',
                'technician_work',
                'operations_final_review',
                'closed',
                'cancelled'
            ])->default('operations_review');

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reported_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('station_id');
            $table->index('status');
            $table->index('department_id');
            $table->index('assigned_to');
            $table->index('current_step');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};