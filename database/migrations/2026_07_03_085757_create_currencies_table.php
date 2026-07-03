<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // USD, EUR, IDR
            $table->string('name', 50);
            $table->string('symbol', 10);
            $table->decimal('exchange_rate_to_usd', 15, 6);
            $table->decimal('exchange_rate_to_eur', 15, 6)->nullable();
            $table->timestamp('rate_updated_at');
            $table->decimal('daily_change', 10, 4)->nullable(); // %
            $table->decimal('weekly_change', 10, 4)->nullable();
            $table->decimal('monthly_change', 10, 4)->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('rate_updated_at');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
