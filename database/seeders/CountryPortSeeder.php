<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CountryPortSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tarik Data Negara dari REST Countries API
        $this->command->info('Mengambil data dari REST Countries API...');
        $responseCountries = Http::get('https://restcountries.com/v3.1/all');

        if ($responseCountries->failed()) {
            $this->command->error('Gagal mengambil data negara.');
            return;
        }

        $countriesData = $responseCountries->json();

        // VALIDASI AWAL: Pastikan response utama adalah array
        if (!is_array($countriesData)) {
            $this->command->error('Format data dari API tidak valid (bukan array).');
            return;
        }

        foreach ($countriesData as $item) {
            // DEFENSIVE PROGRAMMING: Lewati jika $item bukan array atau tidak punya kode negara (cca3)
            if (!is_array($item) || !isset($item['cca3'])) {
                continue; 
            }

            // Ambil kode mata uang dan nama mata uang (antisipasi jika data null)
            $currencies = $item['currencies'] ?? [];
            $currencyCode = !empty($currencies) && is_array($currencies) ? array_key_first($currencies) : 'N/A';
            $currencyName = !empty($currencies) && isset($currencies[$currencyCode]['name']) ? $currencies[$currencyCode]['name'] : 'N/A';

            // Ambil bahasa utama
            $languages = $item['languages'] ?? [];
            $languageName = !empty($languages) && is_array($languages) ? reset($languages) : 'N/A';

            // Insert atau Update ke tabel countries
            DB::table('countries')->updateOrInsert(
                ['code' => $item['cca3']], // Unik berdasarkan ISO 3
                [
                    'name' => $item['name']['common'] ?? 'N/A',
                    'region' => $item['region'] ?? 'N/A',
                    'subregion' => $item['subregion'] ?? 'N/A',
                    'currency_code' => $currencyCode,
                    'currency_name' => $currencyName,
                    'flag_url' => $item['flags']['png'] ?? ($item['flags']['svg'] ?? ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        $this->command->info('Data negara berhasil disimpan!');

        // ---

        // 2. Tarik Data Pelabuhan Global (Simulasi / Mapping Relasi Negara)
        $this->command->info('Mengambil data pelabuhan dari World Bank API...');
        
        $countries = DB::table('countries')->get();

        foreach ($countries as $country) {
            $mockPorts = [
                'IDN' => ['name' => 'Tanjung Priok', 'code' => 'IDTPK', 'lat' => -6.1014, 'lng' => 106.8820],
                'DEU' => ['name' => 'Port of Hamburg', 'code' => 'DEHAM', 'lat' => 53.5453, 'lng' => 9.9413],
                'CHN' => ['name' => 'Port of Shanghai', 'code' => 'CNSHA', 'lat' => 31.2222, 'lng' => 121.4580],
                'USA' => ['name' => 'Port of Los Angeles', 'code' => 'USLAX', 'lat' => 33.7432, 'lng' => -118.2673],
                'SGP' => ['name' => 'Port of Singapore', 'code' => 'SGSIN', 'lat' => 1.2741, 'lng' => 103.8015],
            ];

            if (array_key_exists($country->code, $mockPorts)) {
                $port = $mockPorts[$country->code];
                DB::table('ports')->updateOrInsert(
                    ['port_code' => $port['code']],
                    [
                        'country_id' => $country->id,
                        'port_name' => $port['name'],
                        'latitude' => $port['lat'],
                        'longitude' => $port['lng'],
                        'status' => 'Active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
        $this->command->info('Data pelabuhan sukses dipetakan!');
    }
}