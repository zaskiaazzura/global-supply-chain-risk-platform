<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('source', 100);
            $table->string('author', 100)->nullable();
            $table->string('url')->unique(); // ← Cara SIMPLE, tanpa custom name
            // ATAU pakai custom name yang berbeda:
            // $table->unique('url', 'news_cache_url_idx');
            $table->string('image_url')->nullable();
            $table->timestamp('published_at');
            $table->string('category', 50);
            $table->json('tags')->nullable();
            $table->integer('positive_score')->default(0);
            $table->integer('negative_score')->default(0);
            $table->integer('neutral_score')->default(0);
            $table->string('sentiment', 20)->nullable();
            $table->decimal('sentiment_score', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index('country_id');
            $table->index('category');
            $table->index('sentiment');
            $table->index('published_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_cache');
    }
};