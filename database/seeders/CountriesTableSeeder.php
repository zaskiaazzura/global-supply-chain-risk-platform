<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CountriesTableSeeder extends Seeder
{
    public function run()
    {
        $this->info('🌍 Seeding countries...');

        // Baca file JSON
        $path = database_path('seeders/data/countries.json');
        
        if (!File::exists($path)) {
            $this->error('❌ File countries.json tidak ditemukan!');
            return;
        }

        $content = File::get($path);
        $countries = json_decode($content, true);

        if (!$countries || !is_array($countries)) {
            $this->error('❌ Gagal parsing JSON!');
            return;
        }

        $this->info('📊 Total data: ' . count($countries));

        $count = 0;
        $failed = 0;

        foreach ($countries as $data) {
            try {
                // ============================================
                // FORMAT REST Countries v3.1 (YANG KAMU PUNYA)
                // ============================================
                
                // 1. Ambil kode negara (WAJIB)
                $code = $data['cca3'] ?? null;
                if (!$code) {
                    $failed++;
                    continue;
                }

                // 2. Ambil nama negara
                $name = $data['name']['common'] ?? $data['name']['official'] ?? null;
                if (!$name) {
                    $name = $code; // Fallback: pakai kode sebagai nama
                }

                // 3. Ambil alpha2
                $alpha2 = $data['cca2'] ?? null;

                // 4. Ambil ibu kota
                $capital = null;
                if (isset($data['capital']) && is_array($data['capital']) && !empty($data['capital'])) {
                    $capital = $data['capital'][0];
                } elseif (isset($data['capital']) && is_string($data['capital'])) {
                    $capital = $data['capital'];
                }

                // 5. Ambil mata uang (dengan fallback)
                $currencyCode = null;
                $currencySymbol = null;
                if (isset($data['currencies']) && is_array($data['currencies']) && !empty($data['currencies'])) {
                    $currencyKeys = array_keys($data['currencies']);
                    $currencyCode = $currencyKeys[0] ?? null;
                    if ($currencyCode && isset($data['currencies'][$currencyCode]['symbol'])) {
                        $currencySymbol = $data['currencies'][$currencyCode]['symbol'];
                    }
                }

                // FALLBACK: Jika tidak ada currency, pakai 'N/A'
                if (!$currencyCode) {
                    $currencyCode = 'N/A';
                    $currencySymbol = '';
                }

                // 6. Ambil region
                $region = $data['region'] ?? null;
                $subregion = $data['subregion'] ?? null;

                // 7. Ambil koordinat
                $latitude = isset($data['latlng'][0]) ? $data['latlng'][0] : null;
                $longitude = isset($data['latlng'][1]) ? $data['latlng'][1] : null;

                // 8. Ambil populasi
                $population = $data['population'] ?? null;

                // 9. Ambil timezone
                $timezone = null;
                if (isset($data['timezones']) && is_array($data['timezones']) && !empty($data['timezones'])) {
                    $timezone = $data['timezones'][0];
                }

                // 10. Ambil bahasa
                $languages = isset($data['languages']) && is_array($data['languages']) ? $data['languages'] : [];

                // 11. Ambil borders
                $borders = isset($data['borders']) && is_array($data['borders']) ? $data['borders'] : [];

                // 12. Ambil flag
                $flagUrl = null;
                if (isset($data['flags']['png'])) {
                    $flagUrl = $data['flags']['png'];
                } elseif (isset($data['flag'])) {
                    $flagUrl = $data['flag'];
                }

                // ============================================
                // Siapkan data untuk insert
                // ============================================
                $countryData = [
                    'name' => $name,
                    'code' => $code,
                    'alpha2' => $alpha2,
                    'capital' => $capital,
                    'currency' => $currencyCode,
                    'currency_symbol' => $currencySymbol,
                    'region' => $region,
                    'subregion' => $subregion,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'population' => $population,
                    'timezone' => $timezone,
                    'languages' => json_encode($languages),
                    'borders' => json_encode($borders),
                    'flag_url' => $flagUrl,
                ];

                // Insert atau update
                Country::updateOrCreate(
                    ['code' => $countryData['code']],
                    $countryData
                );
                
                $count++;

                if ($count % 50 == 0) {
                    $this->info("   ... {$count} negara berhasil");
                }

            } catch (\Exception $e) {
                $failed++;
                if ($failed <= 5) {
                    $this->warn("   ⚠️ Error pada data ke-" . ($count + $failed) . ": " . $e->getMessage());
                }
            }
        }

        $this->newLine();
        $this->info("✅ Berhasil: {$count} negara");
        if ($failed > 0) {
            $this->warn("⚠️ Gagal: {$failed} data");
        }
    }

    private function info($message)
    {
        if ($this->command) {
            $this->command->info($message);
        }
    }

    private function error($message)
    {
        if ($this->command) {
            $this->command->error($message);
        }
    }

    private function warn($message)
    {
        if ($this->command) {
            $this->command->warn($message);
        }
    }

    private function newLine()
    {
        if ($this->command) {
            $this->command->newLine();
        }
    }
}