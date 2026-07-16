<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('source')->nullable()->after('author');
            $table->string('url')->nullable()->after('source');
            $table->integer('positive_score')->default(0)->after('view_count');
            $table->integer('negative_score')->default(0)->after('positive_score');
            $table->integer('neutral_score')->default(0)->after('negative_score');
            $table->string('sentiment')->nullable()->after('neutral_score');
            $table->decimal('sentiment_score', 5, 2)->nullable()->after('sentiment');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['source', 'url', 'positive_score', 'negative_score', 'neutral_score', 'sentiment', 'sentiment_score']);
        });
    }
};