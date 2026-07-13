<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('news_cache', function (Blueprint $table) {
            $table->string('source', 100)->nullable()->change();
            $table->string('author', 100)->nullable()->change();
            $table->text('content')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('news_cache', function (Blueprint $table) {
            $table->string('source', 100)->nullable(false)->change();
            $table->string('author', 100)->nullable(false)->change();
            $table->text('content')->nullable(false)->change();
        });
    }
};