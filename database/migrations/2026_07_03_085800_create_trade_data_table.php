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
        Schema::create('trade_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->foreignId('partner_country_id')->constrained('countries')->onDelete('cascade');
            $table->decimal('export_value_usd', 15, 2);
            $table->decimal('import_value_usd', 15, 2);
            $table->decimal('trade_balance_usd', 15, 2);
            $table->json('export_commodities')->nullable();
            $table->json('import_commodities')->nullable();
            $table->integer('year');
            $table->string('quarter', 10)->nullable();
            $table->timestamps();
            
            $table->unique(['country_id', 'partner_country_id', 'year', 'quarter']);
            $table->index('country_id');
            $table->index('year');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_data');
    }
};
