<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->string('name');
            $table->string('code')->unique()->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('parent_company_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};