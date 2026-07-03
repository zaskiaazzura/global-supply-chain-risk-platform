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
        Schema::create('country_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('country_1_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('country_2_id')->constrained('countries')->onDelete('cascade');
            $table->string('comparison_name', 100)->nullable();
            $table->json('comparison_data')->nullable();
            $table->timestamps();
            
            $table->unique(
                ['user_id', 'country_1_id', 'country_2_id'],
                'country_comp_user_c1_c2_unique'
            );
            $table->index('user_id');
            $table->index(['country_1_id', 'country_2_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_comparisons');
    }
};
