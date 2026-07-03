<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RiskWeight;

class RiskWeightsSeeder extends Seeder
{
    public function run()
    {
        $weights = [
            [
                'risk_factor' => 'weather',
                'weight_percentage' => 30.00,
                'description' => 'Weather risk from storms, hurricanes, etc.'
            ],
            [
                'risk_factor' => 'inflation',
                'weight_percentage' => 20.00,
                'description' => 'Inflation rate risk'
            ],
            [
                'risk_factor' => 'political_news',
                'weight_percentage' => 40.00,
                'description' => 'Political and geopolitical news sentiment'
            ],
            [
                'risk_factor' => 'currency',
                'weight_percentage' => 10.00,
                'description' => 'Currency exchange rate volatility'
            ],
        ];

        foreach ($weights as $weight) {
            RiskWeight::firstOrCreate(
                ['risk_factor' => $weight['risk_factor']],
                [
                    'weight_percentage' => $weight['weight_percentage'],
                    'description' => $weight['description']
                ]
            );
        }
    }
}