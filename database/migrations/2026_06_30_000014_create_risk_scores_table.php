<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('weather_score', 6, 2)->default(0);
            $table->decimal('inflation_score', 6, 2)->default(0);
            $table->decimal('currency_score', 6, 2)->default(0);
            $table->decimal('news_score', 6, 2)->default(0);
            $table->decimal('total_score', 6, 2);
            $table->enum('risk_level', ['Low', 'Medium', 'High'])->default('Low');
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index(['country_id', 'calculated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
