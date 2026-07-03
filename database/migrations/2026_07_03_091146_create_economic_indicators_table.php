<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('economic_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('indicator_type', 50);
            $table->decimal('value', 15, 4);
            $table->string('unit', 20);
            $table->integer('year');
            $table->string('quarter', 10)->nullable();
            $table->string('source', 100)->nullable();
            $table->timestamps();
            
            // Custom unique constraint name - kunci utama perbaikan!
            $table->unique(
                ['country_id', 'indicator_type', 'year', 'quarter'], 
                'eco_ind_country_type_year_qtr_unique'
            );
            
            $table->index('country_id');
            $table->index('indicator_type');
            $table->index('year');
        });
    }

    public function down()
    {
        Schema::dropIfExists('economic_indicators');
    }
};