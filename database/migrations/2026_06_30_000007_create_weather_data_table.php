<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('temperature', 6, 2)->nullable();
            $table->decimal('rainfall', 8, 2)->nullable();
            $table->decimal('wind_speed', 8, 2)->nullable();
            $table->enum('storm_risk_level', ['low', 'medium', 'high'])->default('low');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['country_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
