<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            // Ubah kolom currency dan currency_symbol menjadi nullable
            $table->string('currency', 10)->nullable()->change();
            $table->string('currency_symbol', 10)->nullable()->change();
            
            // Ubah juga kolom lain yang mungkin null
            $table->string('capital', 100)->nullable()->change();
            $table->string('subregion', 50)->nullable()->change();
            $table->string('timezone', 50)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('currency', 10)->nullable(false)->change();
            $table->string('currency_symbol', 10)->nullable(false)->change();
            $table->string('capital', 100)->nullable(false)->change();
            $table->string('subregion', 50)->nullable(false)->change();
            $table->string('timezone', 50)->nullable(false)->change();
        });
    }
};