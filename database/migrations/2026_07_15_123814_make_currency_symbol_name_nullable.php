<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('name', 50)->nullable()->change();
            $table->string('symbol', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('name', 50)->nullable(false)->change();
            $table->string('symbol', 10)->nullable(false)->change();
        });
    }
};