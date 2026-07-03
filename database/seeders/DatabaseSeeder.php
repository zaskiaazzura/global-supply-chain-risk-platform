<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PositiveWordsSeeder::class,
            NegativeWordsSeeder::class,
            RiskWeightsSeeder::class,
            CountriesTableSeeder::class,
        ]);
    }
}