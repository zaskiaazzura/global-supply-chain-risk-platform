<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_sentiment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->unique()->constrained('news_articles')->cascadeOnDelete();
            $table->unsignedInteger('positive_count')->default(0);
            $table->unsignedInteger('negative_count')->default(0);
            $table->enum('sentiment_result', ['Positive', 'Neutral', 'Negative']);
            $table->decimal('sentiment_score', 5, 2)->nullable(); // e.g. 60.00 (%)
            $table->timestamp('analyzed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_sentiment');
    }
};
