<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->change();
            $table->text('content')->change(); // Juga ubah content ke text
            $table->text('source')->nullable()->change(); // Source juga
            $table->text('author')->nullable()->change(); // Author juga
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('excerpt', 500)->nullable()->change();
            $table->text('content')->change();
            $table->string('source', 100)->nullable()->change();
            $table->string('author', 100)->nullable()->change();
        });
    }
};