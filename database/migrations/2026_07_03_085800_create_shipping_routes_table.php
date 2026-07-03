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
        Schema::create('shipping_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_port_id')->constrained('ports')->onDelete('cascade');
            $table->foreignId('destination_port_id')->constrained('ports')->onDelete('cascade');
            $table->string('route_name', 100);
            $table->integer('distance_nautical_miles');
            $table->integer('estimated_days');
            $table->decimal('average_cost_usd', 15, 2);
            $table->decimal('current_cost_usd', 15, 2)->nullable();
            $table->json('shipping_lines')->nullable();
            $table->integer('frequency_per_week')->default(1);
            $table->decimal('reliability_score', 5, 2)->nullable(); // 0-100
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(
                ['origin_port_id', 'destination_port_id'],
                'shipping_routes_origin_dest_unique' // Nama pendek
            );            
            $table->index('origin_port_id');
            $table->index('destination_port_id');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_routes');
    }
};
