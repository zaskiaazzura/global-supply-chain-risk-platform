<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_factors', function (Blueprint $table) {
            $table->id();
            $table->string('factor_name')->unique(); // Weather, Inflation, News, Currency
            $table->decimal('weight_percentage', 5, 2); // e.g. 30.00
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_factors');
    }
};
