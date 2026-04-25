<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();

            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'under_maintenance',
                'stopped'
            ])->default('active');

            $table->timestamps();

            $table->index('company_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};