<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('language');
            $table->timestamps();

            $table->unique(['country_id', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_languages');
    }
};
