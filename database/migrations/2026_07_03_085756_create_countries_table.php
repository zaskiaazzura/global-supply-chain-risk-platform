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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 3)->unique(); // ISO 3166-1 alpha-3
            $table->string('alpha2', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('capital', 100)->nullable();
            $table->string('currency', 10);
            $table->string('currency_symbol', 10)->nullable();
            $table->string('region', 50);
            $table->string('subregion', 50)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->bigInteger('population')->nullable();
            $table->decimal('gdp', 15, 2)->nullable(); // GDP in USD
            $table->decimal('inflation_rate', 5, 2)->nullable(); // %
            $table->decimal('unemployment_rate', 5, 2)->nullable();
            $table->string('timezone', 50)->nullable();
            $table->string('flag_url')->nullable();
            $table->json('languages')->nullable();
            $table->json('borders')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
