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
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->decimal('weather_risk', 5, 2); // 0-100
            $table->decimal('inflation_risk', 5, 2);
            $table->decimal('currency_risk', 5, 2);
            $table->decimal('political_risk', 5, 2);
            $table->decimal('logistics_risk', 5, 2);
            $table->decimal('total_risk_score', 5, 2); // Weighted sum
            $table->string('risk_level', 20); // Low, Medium, High, Critical
            $table->json('risk_factors')->nullable(); // Detailed breakdown
            $table->json('recommendations')->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();
            
            $table->index('country_id');
            $table->index('total_risk_score');
            $table->index('risk_level');
            $table->index('calculated_at');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
