<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_cache', function (Blueprint $table) {
            $table->id();
            $table->string('api_name'); // open-meteo, world-bank, gnews, exchangerate, etc.
            $table->string('endpoint_key')->index(); // unique key identifying the cached request
            $table->longText('response_data'); // JSON encoded
            $table->timestamp('expires_at')->useCurrent();
            $table->timestamps();

            $table->unique(['api_name', 'endpoint_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_cache');
    }
};
