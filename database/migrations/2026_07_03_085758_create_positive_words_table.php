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
        Schema::create('positive_words', function (Blueprint $table) {
            $table->id();
            $table->string('word', 50)->unique();
            $table->integer('weight')->default(1); // For scoring
            $table->string('category', 50)->nullable(); // Economy, Logistics, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('word');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positive_words');
    }
};
