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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('preferred_currencies')->nullable();
            $table->json('preferred_countries')->nullable();
            $table->string('dashboard_layout', 50)->default('default');
            $table->string('theme', 20)->default('light');
            $table->json('notification_settings')->nullable();
            $table->timestamps();
            
            $table->unique('user_id', 'user_prefs_user_unique');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
