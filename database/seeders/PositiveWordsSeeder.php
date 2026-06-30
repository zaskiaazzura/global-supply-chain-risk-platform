<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositiveWordsSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            'growth',
            'increase',
            'profit',
            'stable',
            'improve',
            'improvement',
            'recovery',
            'expand',
            'expansion',
            'boost',
            'surge',
            'rally',
            'gain',
            'gains',
            'strong',
            'strengthen',
            'rebound',
            'progress',
            'efficient',
            'efficiency',
            'success',
            'successful',
            'opportunity',
            'partnership',
            'agreement',
            'cooperation',
            'investment',
            'invest',
            'upgrade',
            'breakthrough',
            'resilient',
            'resilience',
            'optimistic',
            'optimism',
            'thrive',
            'thriving',
            'prosper',
            'prosperity',
            'innovation',
            'innovative',
            'record-high',
            'milestone',
            'expedite',
            'streamline',
            'secure',
            'smooth',
            'on-time',
            'reliable',
            'reduce delay',
            'easing',
        ];

        $now = now();

        $data = collect($words)->unique()->map(fn ($word) => [
            'word' => strtolower($word),
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        DB::table('positive_words')->upsert($data, ['word'], ['updated_at']);
    }
}
