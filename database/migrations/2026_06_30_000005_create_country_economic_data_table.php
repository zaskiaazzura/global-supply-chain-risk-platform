<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_economic_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('gdp', 20, 2)->nullable();
            $table->decimal('inflation_rate', 8, 4)->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->decimal('export_value', 20, 2)->nullable();
            $table->decimal('import_value', 20, 2)->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['country_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_economic_data');
    }
};
