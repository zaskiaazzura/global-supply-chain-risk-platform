<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->string('port_type')->nullable(); // sea, river, etc.
            $table->enum('status', ['active', 'congested', 'closed'])->default('active');
            $table->timestamps();

            $table->index('country_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
