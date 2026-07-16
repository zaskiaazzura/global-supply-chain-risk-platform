<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentAnalysisService
{
    /**
     * Analyze sentiment of news articles collection
     */
    public function analyzeNews($news)
    {
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        $totalPositive = 0;
        $totalNegative = 0;
        $totalNeutral = 0;
        $totalArticles = count($news);

        foreach ($news as $article) {
            $text = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
            // ✅ PERBAIKI: HANYA 1 PARAMETER
            $result = $this->analyzeText($text);
            
            $totalPositive += $result['positive'];
            $totalNegative += $result['negative'];
            $totalNeutral += $result['neutral'];
        }

        // Calculate percentages
        $totalWords = $totalPositive + $totalNegative + $totalNeutral;
        
        if ($totalWords > 0) {
            $positivePercent = round(($totalPositive / $totalWords) * 100);
            $negativePercent = round(($totalNegative / $totalWords) * 100);
            $neutralPercent = round(($totalNeutral / $totalWords) * 100);
        } else {
            $positivePercent = 0;
            $negativePercent = 0;
            $neutralPercent = 0;
        }

        // Determine overall sentiment
        $sentiment = 'neutral';
        if ($positivePercent > $negativePercent && $positivePercent > 40) {
            $sentiment = 'positive';
        } elseif ($negativePercent > $positivePercent && $negativePercent > 40) {
            $sentiment = 'negative';
        }

        return [
            'total_articles' => $totalArticles,
            'positive' => $positivePercent,
            'negative' => $negativePercent,
            'neutral' => $neutralPercent,
            'sentiment' => $sentiment,
            'positive_score' => $totalPositive,
            'negative_score' => $totalNegative,
            'neutral_score' => $totalNeutral
        ];
    }

    /**
     * Analyze sentiment of a single text
     */
    public function analyzeText($text)
    {
        // Load words dari database
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        // Bersihkan teks
        $text = strtolower($text);
        $text = preg_replace('/[^a-zA-Z\s]/', '', $text);
        $words = str_word_count($text, 1);
        
        $positive = 0;
        $negative = 0;

        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positive++;
            }
            if (in_array($word, $negativeWords)) {
                $negative++;
            }
        }

        $neutral = count($words) - $positive - $negative;

        return [
            'positive' => $positive,
            'negative' => $negative,
            'neutral' => $neutral,
            'total' => count($words)
        ];
    }

    /**
     * Analyze sentiment of a single article (title + description)
     */
    public function analyzeArticle($title, $description = '')
    {
        $text = $title . ' ' . $description;
        // ✅ PERBAIKI: HANYA 1 PARAMETER
        return $this->analyzeText($text);
    }

    /**
     * Get sentiment score (-1 to 1)
     */
    public function getSentimentScore($text)
    {
        $result = $this->analyzeText($text);
        $total = $result['total'];
        
        if ($total === 0) {
            return 0;
        }

        return ($result['positive'] - $result['negative']) / $total;
    }

    /**
     * Get sentiment label
     */
    public function getSentimentLabel($score)
    {
        if ($score > 0.2) {
            return 'positive';
        } elseif ($score < -0.2) {
            return 'negative';
        }
        return 'neutral';
    }
}