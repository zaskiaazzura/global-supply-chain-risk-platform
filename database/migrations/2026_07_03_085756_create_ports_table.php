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
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 10)->unique(); // UN/LOCODE
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('type', 50); // Sea, River, Lake, etc.
            $table->string('size', 20)->nullable(); // Small, Medium, Large
            $table->integer('max_draft')->nullable(); // in meters
            $table->integer('annual_throughput')->nullable(); // in TEU
            $table->string('timezone', 50)->nullable();
            $table->json('facilities')->nullable(); // Container, Bulk, Liquid, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('country_id');
            $table->index('type');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
