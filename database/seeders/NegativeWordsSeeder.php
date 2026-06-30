<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NegativeWordsSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            'war',
            'crisis',
            'inflation',
            'delay',
            'disaster',
            'decrease',
            'decline',
            'recession',
            'conflict',
            'sanction',
            'sanctions',
            'tension',
            'tensions',
            'shortage',
            'disruption',
            'disrupt',
            'congestion',
            'collapse',
            'default',
            'debt',
            'deficit',
            'unrest',
            'strike',
            'protest',
            'embargo',
            'tariff',
            'tariffs',
            'volatility',
            'volatile',
            'slump',
            'slowdown',
            'layoff',
            'layoffs',
            'bankruptcy',
            'risk',
            'risky',
            'threat',
            'instability',
            'unstable',
            'flood',
            'storm',
            'earthquake',
            'drought',
            'pandemic',
            'outbreak',
            'fraud',
            'corruption',
            'sabotage',
            'blockade',
            'shutdown',
        ];

        $now = now();

        $data = collect($words)->unique()->map(fn ($word) => [
            'word' => strtolower($word),
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        DB::table('negative_words')->upsert($data, ['word'], ['updated_at']);
    }
}
