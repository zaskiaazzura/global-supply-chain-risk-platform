<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('country_id_1')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('country_id_2')->constrained('countries')->cascadeOnDelete();
            $table->timestamp('compared_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_comparisons');
    }
};
