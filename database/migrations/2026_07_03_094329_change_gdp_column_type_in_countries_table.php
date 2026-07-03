<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            // Ubah dari decimal(15,2) menjadi decimal(20,2)
            $table->decimal('gdp', 20, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->decimal('gdp', 15, 2)->change();
        });
    }
};