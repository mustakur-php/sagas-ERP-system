<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_closing_collections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_closing_id')
                ->constrained('daily_closings')
                ->cascadeOnDelete();

            $table->string('collection_type');
            // cash / card / company_account / wallet / other

            $table->string('provider_name')->nullable();
            // مثال: مدى / بترو آب / سيارة

            $table->decimal('amount', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_closing_collections');
    }
};