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
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('temperature', 5, 2); // Celsius
            $table->decimal('feels_like', 5, 2)->nullable();
            $table->decimal('humidity', 5, 2); // %
            $table->decimal('wind_speed', 5, 2); // m/s
            $table->decimal('wind_gust', 5, 2)->nullable();
            $table->string('wind_direction', 10)->nullable();
            $table->decimal('precipitation', 8, 2); // mm
            $table->string('weather_condition', 50); // Clear, Rain, Storm, etc.
            $table->string('weather_icon')->nullable();
            $table->integer('cloud_cover')->nullable(); // %
            $table->integer('visibility')->nullable(); // meters
            $table->decimal('uv_index', 4, 1)->nullable();
            $table->timestamp('weather_timestamp');
            $table->json('forecast')->nullable(); // 7-day forecast
            $table->boolean('is_storm_warning')->default(false);
            $table->timestamps();
            
            $table->index('country_id');
            $table->index('weather_timestamp');
            $table->index('weather_condition');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};
