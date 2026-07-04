<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\RiskScore;
use App\Services\RiskScoreService;

class RiskScoresSeeder extends Seeder
{
    protected $riskService;

    public function __construct(RiskScoreService $riskService)
    {
        $this->riskService = $riskService;
    }

    public function run()
    {
        $countries = Country::all();
        
        $this->command->info('🔄 Calculating risk scores for all countries...');
        
        $count = 0;
        foreach ($countries as $country) {
            try {
                $this->riskService->calculateRiskScore($country);
                $count++;
                
                if ($count % 10 == 0) {
                    $this->command->info("   ... {$count} countries processed");
                }
            } catch (\Exception $e) {
                $this->command->warn("   ⚠️ Failed for {$country->name}: " . $e->getMessage());
            }
        }
        
        $this->command->info("✅ Risk scores calculated for {$count} countries!");
    }
}