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
        Schema::create('api_call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('api_name', 50);
            $table->string('endpoint', 255);
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('response_status');
            $table->integer('response_time_ms');
            $table->boolean('is_successful')->default(true);
            $table->string('error_message', 255)->nullable();
            $table->timestamps();
            
            $table->index('api_name');
            $table->index('is_successful');
            $table->index('created_at');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_call_logs');
    }
};
