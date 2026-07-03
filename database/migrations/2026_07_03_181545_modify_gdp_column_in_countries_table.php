<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            // Ubah kolom gdp menjadi nullable
            $table->decimal('gdp', 20, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->decimal('gdp', 20, 2)->nullable(false)->change();
        });
    }
};