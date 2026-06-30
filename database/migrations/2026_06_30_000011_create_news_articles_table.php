<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('source_url')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->enum('category', ['logistics', 'trade', 'economy', 'geopolitics'])->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('fetched_at')->useCurrent();
            $table->timestamps();

            $table->index(['country_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
