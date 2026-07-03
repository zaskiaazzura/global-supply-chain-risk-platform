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
        Schema::create('historical_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 3);
            $table->decimal('rate_to_usd', 15, 6);
            $table->date('rate_date');
            $table->decimal('daily_change', 10, 4)->nullable();
            $table->timestamps();
            
            $table->unique(
                ['currency_code', 'rate_date'],
                'hist_rates_currency_date_unique'
            );
            $table->index('currency_code');
            $table->index('rate_date');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_rates');
    }
};
