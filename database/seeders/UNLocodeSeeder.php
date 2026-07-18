<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Support\Facades\File;

class UNLocodeSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🌍 Importing UN/LOCODE ports...');

        $files = [
            'unlocode_part1.csv',
            'unlocode_part2.csv',
            'unlocode_part3.csv',
        ];

        $saved = 0;
        $skipped = 0;
        $countries = Country::all()->keyBy('alpha2');

        foreach ($files as $filename) {
            $filePath = database_path('seeders/data/' . $filename);

            if (!File::exists($filePath)) {
                $this->command->warn("⚠️  File tidak ditemukan: {$filename}");
                continue;
            }

            $this->command->info("📂 Processing: {$filename}");

            $file = fopen($filePath, 'r');
            if (!$file) {
                $this->command->error("❌ Gagal membuka file: {$filename}");
                continue;
            }

            // Skip header (baris pertama)
            $headers = fgetcsv($file);

            while (($data = fgetcsv($file)) !== false) {
                if (count($data) < 10) {
                    $skipped++;
                    continue;
                }

                $countryAlpha2 = trim($data[1] ?? '');   // Index 1 = Country Code
                $locationCode = trim($data[2] ?? '');    // Index 2 = Location Code
                $name = trim($data[3] ?? '');            // Index 3 = Name
                $function = trim($data[6] ?? '');        // Index 6 = Function
                $coordinates = trim($data[10] ?? '');    // Index 10 = Coordinates

                if (empty($countryAlpha2) || empty($locationCode) || empty($name)) {
                    $skipped++;
                    continue;
                }

                // Hanya pelabuhan (fungsi mengandung '1')
                if (strpos($function, '1') === false) {
                    $skipped++;
                    continue;
                }

                $country = $countries[$countryAlpha2] ?? null;
                if (!$country) {
                    $skipped++;
                    continue;
                }

                // Parse koordinat
                $coords = $this->parseCoordinates($coordinates);
                if (!$coords) {
                    $skipped++;
                    continue;
                }

                try {
                    Port::updateOrCreate(
                        [
                            'code' => $countryAlpha2 . $locationCode,
                            'country_id' => $country->id,
                        ],
                        [
                            'name' => $name,
                            'latitude' => $coords['lat'],
                            'longitude' => $coords['lng'],
                            'city' => $name,
                            'type' => 'Sea',
                            'size' => 'Medium'
                        ]
                    );
                    $saved++;
                } catch (\Exception $e) {
                    $skipped++;
                }

                if ($saved % 100 == 0 && $saved > 0) {
                    $this->command->info("   ... {$saved} ports imported");
                }
            }

            fclose($file);
        }

        $this->command->info("\n✅ Berhasil import: {$saved} pelabuhan");
        $this->command->warn("⚠️  Dilewati: {$skipped} data");
    }

    private function parseCoordinates($coordString)
    {
        $coordString = trim($coordString);
        if (empty($coordString)) return null;

        $parts = explode(' ', $coordString);
        if (count($parts) != 2) return null;

        $latPart = $parts[0];
        $lonPart = $parts[1];

        if (preg_match('/^(\d{2})(\d{2})([NS])$/', $latPart, $matches)) {
            $lat = $this->toDecimal($matches[1], $matches[2], $matches[3]);
        } else {
            return null;
        }

        if (preg_match('/^(\d{3})(\d{2})([EW])$/', $lonPart, $matches)) {
            $lng = $this->toDecimal($matches[1], $matches[2], $matches[3]);
        } else {
            return null;
        }

        return ['lat' => $lat, 'lng' => $lng];
    }

    private function toDecimal($degrees, $minutes, $direction)
    {
        $decimal = floatval($degrees) + (floatval($minutes) / 60);
        if ($direction === 'S' || $direction === 'W') {
            $decimal = -$decimal;
        }
        return $decimal;
    }
}