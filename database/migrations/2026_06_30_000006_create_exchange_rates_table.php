<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->foreignId('target_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->decimal('rate', 18, 6);
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['base_currency_id', 'target_currency_id', 'recorded_at'], 'exchange_rates_pair_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
