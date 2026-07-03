<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NegativeWord;

class NegativeWordsSeeder extends Seeder
{
    public function run()
    {
        $words = [
            ['word' => 'war', 'category' => 'economy'],
            ['word' => 'crisis', 'category' => 'economy'],
            ['word' => 'inflation', 'category' => 'economy'],
            ['word' => 'delay', 'category' => 'economy'],
            ['word' => 'disaster', 'category' => 'economy'],
            ['word' => 'recession', 'category' => 'economy'],
            ['word' => 'collapse', 'category' => 'economy'],
            ['word' => 'decline', 'category' => 'economy'],
            ['word' => 'drop', 'category' => 'economy'],
            ['word' => 'delay', 'category' => 'logistics'],
            ['word' => 'congestion', 'category' => 'logistics'],
            ['word' => 'disruption', 'category' => 'logistics'],
            ['word' => 'accident', 'category' => 'logistics'],
            ['word' => 'damage', 'category' => 'logistics'],
            ['word' => 'shortage', 'category' => 'logistics'],
            ['word' => 'storm', 'category' => 'weather'],
            ['word' => 'hurricane', 'category' => 'weather'],
            ['word' => 'typhoon', 'category' => 'weather'],
            ['word' => 'flood', 'category' => 'weather'],
            ['word' => 'earthquake', 'category' => 'weather'],
            ['word' => 'tsunami', 'category' => 'weather'],
            ['word' => 'cyclone', 'category' => 'weather'],
            ['word' => 'bad', 'category' => 'general'],
            ['word' => 'negative', 'category' => 'general'],
            ['word' => 'fail', 'category' => 'general'],
            ['word' => 'fear', 'category' => 'general'],
            ['word' => 'risk', 'category' => 'general'],
            ['word' => 'danger', 'category' => 'general'],
            ['word' => 'conflict', 'category' => 'general'],
            ['word' => 'sanction', 'category' => 'general'],
            ['word' => 'blockade', 'category' => 'general'],
        ];

        foreach ($words as $word) {
            // Cek apakah sudah ada, jika belum maka buat
            NegativeWord::firstOrCreate(
                ['word' => $word['word']],
                ['category' => $word['category']]
            );
        }
    }
}