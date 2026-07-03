<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OpenMeteoService;
use App\Services\RestCountriesService;
use App\Services\ExchangeRateService;
use App\Services\GNewsService;
use App\Services\WorldBankService;
use App\Services\MarineTrafficService;

class TestApi extends Command
{
    protected $signature = 'api:test';
    protected $description = 'Test all API connections';

    public function handle(
        OpenMeteoService $openMeteo,
        RestCountriesService $restCountries,
        ExchangeRateService $exchangeRate,
        GNewsService $gNews,
        WorldBankService $worldBank,
        MarineTrafficService $marineTraffic
    ) {
        $this->info('=== TESTING API CONNECTIONS ===');
        $this->newLine();

        $this->testOpenMeteo($openMeteo);
        $this->testRestCountries($restCountries);
        $this->testExchangeRate($exchangeRate);
        $this->testGNews($gNews);
        $this->testWorldBank($worldBank);
        $this->testMarineTraffic($marineTraffic);

        $this->newLine();
        $this->info('=== ALL TESTS COMPLETED ===');
    }

    private function testOpenMeteo($service)
    {
        $this->line('[1] Testing Open-Meteo API...');
        
        try {
            $weather = $service->getCurrentWeather(-6.2088, 106.8456);
            
            if ($weather && isset($weather['current_weather'])) {
                $this->info('    [OK] Connected!');
                $temp = $weather['current_weather']['temperature'] ?? 'N/A';
                $wind = $weather['current_weather']['windspeed'] ?? 'N/A';
                $this->line("    Temperature: {$temp} C");
                $this->line("    Wind Speed: {$wind} km/h");
            } else {
                $this->error('    [FAIL] No data returned');
            }
        } catch (\Exception $e) {
            $this->error('    [ERROR] ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testRestCountries($service)
    {
        $this->line('[2] Testing REST Countries API...');
        
        try {
            $country = $service->getCountryByCode('IDN');
            
            if ($country && isset($country[0])) {
                $this->info('    [OK] Connected!');
                $name = $country[0]['name']['common'] ?? 'N/A';
                $capital = $country[0]['capital'][0] ?? 'N/A';
                $pop = number_format($country[0]['population'] ?? 0);
                $this->line("    Country: {$name}");
                $this->line("    Capital: {$capital}");
                $this->line("    Population: {$pop}");
            } else {
                $this->error('    [FAIL] No data returned');
            }
        } catch (\Exception $e) {
            $this->error('    [ERROR] ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testExchangeRate($service)
    {
        $this->line('[3] Testing Exchange Rate API...');
        
        try {
            $rates = $service->getLatestRates('USD');
            
            if ($rates && isset($rates['rates'])) {
                $this->info('    [OK] Connected!');
                $this->line("    Base: {$rates['base']}");
                $this->line("    EUR: {$rates['rates']['EUR']}");
                $this->line("    GBP: {$rates['rates']['GBP']}");
                $this->line("    JPY: {$rates['rates']['JPY']}");
                $this->line("    IDR: {$rates['rates']['IDR']}");
            } else {
                $this->error('    [FAIL] No data returned');
            }
        } catch (\Exception $e) {
            $this->error('    [ERROR] ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testGNews($service)
    {
        $this->line('[4] Testing GNews API...');
        
        try {
            $news = $service->searchNews('logistics', 3);
            
            if ($news && count($news) > 0) {
                $this->info('    [OK] Connected!');
                $this->line("    Found " . count($news) . " articles");
                $this->line("    First: " . ($news[0]['title'] ?? 'N/A'));
            } else {
                $this->warn('    [WARN] No articles found');
            }
        } catch (\Exception $e) {
            $this->error('    [ERROR] ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testWorldBank($service)
    {
        $this->line('[5] Testing World Bank API...');
        
        try {
            $data = $service->getAllIndicators('IDN');
            
            if ($data && ($data['gdp'] || $data['population'])) {
                $this->info('    [OK] Connected!');
                $this->line("    GDP: $" . number_format($data['gdp'] ?? 0));
                $this->line("    Inflation: " . ($data['inflation'] ?? 'N/A') . "%");
                $this->line("    Population: " . number_format($data['population'] ?? 0));
            } else {
                $this->error('    [FAIL] No data returned');
            }
        } catch (\Exception $e) {
            $this->error('    [ERROR] ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testMarineTraffic($service)
    {
        $this->line('[6] Testing Marine Traffic API...');
        
        try {
            $ports = $service->getPorts();
            
            if ($ports && !empty($ports)) {
                $this->info('    [OK] Connected!');
                $this->line("    Found " . count($ports) . " ports");
                if (isset($ports[0])) {
                    $this->line("    First port: " . ($ports[0]['name'] ?? 'N/A'));
                }
            } else {
                $this->warn('    [WARN] No ports returned');
            }
        } catch (\Exception $e) {
            $this->error('    [ERROR] ' . $e->getMessage());
        }
        $this->newLine();
    }
}