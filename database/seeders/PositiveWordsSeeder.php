<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PositiveWord;

class PositiveWordsSeeder extends Seeder
{
    public function run()
    {
        $words = [
            ['word' => 'growth', 'category' => 'economy'],
            ['word' => 'increase', 'category' => 'economy'],
            ['word' => 'profit', 'category' => 'economy'],
            ['word' => 'stable', 'category' => 'economy'],
            ['word' => 'improve', 'category' => 'economy'],
            ['word' => 'recovery', 'category' => 'economy'],
            ['word' => 'boom', 'category' => 'economy'],
            ['word' => 'surge', 'category' => 'economy'],
            ['word' => 'rising', 'category' => 'economy'],
            ['word' => 'efficient', 'category' => 'logistics'],
            ['word' => 'fast', 'category' => 'logistics'],
            ['word' => 'smooth', 'category' => 'logistics'],
            ['word' => 'reliable', 'category' => 'logistics'],
            ['word' => 'safe', 'category' => 'logistics'],
            ['word' => 'innovative', 'category' => 'logistics'],
            ['word' => 'agreement', 'category' => 'trade'],
            ['word' => 'partnership', 'category' => 'trade'],
            ['word' => 'cooperation', 'category' => 'trade'],
            ['word' => 'export', 'category' => 'trade'],
            ['word' => 'import', 'category' => 'trade'],
            ['word' => 'good', 'category' => 'general'],
            ['word' => 'great', 'category' => 'general'],
            ['word' => 'positive', 'category' => 'general'],
            ['word' => 'success', 'category' => 'general'],
            ['word' => 'win', 'category' => 'general'],
            ['word' => 'peace', 'category' => 'general'],
        ];

        foreach ($words as $word) {
            // Cek apakah sudah ada, jika belum maka buat
            PositiveWord::firstOrCreate(
                ['word' => $word['word']],
                ['category' => $word['category']]
            );
        }
    }
}