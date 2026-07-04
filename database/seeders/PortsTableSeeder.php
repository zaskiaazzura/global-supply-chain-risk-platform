<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port;
use App\Models\Country;

class PortsTableSeeder extends Seeder
{
    public function run()
    {
        $ports = [
            // Indonesia
            ['name' => 'Port of Tanjung Priok', 'code' => 'IDTPP', 'city' => 'Jakarta', 'type' => 'Sea', 'size' => 'Large', 'latitude' => -6.1041, 'longitude' => 106.8778, 'country_code' => 'IDN'],
            ['name' => 'Port of Surabaya', 'code' => 'IDSUB', 'city' => 'Surabaya', 'type' => 'Sea', 'size' => 'Large', 'latitude' => -7.2115, 'longitude' => 112.7205, 'country_code' => 'IDN'],
            ['name' => 'Port of Belawan', 'code' => 'IDBLW', 'city' => 'Medan', 'type' => 'Sea', 'size' => 'Medium', 'latitude' => 3.7779, 'longitude' => 98.7081, 'country_code' => 'IDN'],
            ['name' => 'Port of Makassar', 'code' => 'IDMAK', 'city' => 'Makassar', 'type' => 'Sea', 'size' => 'Medium', 'latitude' => -5.1145, 'longitude' => 119.4165, 'country_code' => 'IDN'],
            ['name' => 'Port of Balikpapan', 'code' => 'IDBPN', 'city' => 'Balikpapan', 'type' => 'Sea', 'size' => 'Medium', 'latitude' => -1.2442, 'longitude' => 116.8621, 'country_code' => 'IDN'],
            
            // Singapore
            ['name' => 'Port of Singapore', 'code' => 'SGSIN', 'city' => 'Singapore', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 1.2903, 'longitude' => 103.8520, 'country_code' => 'SGP'],
            
            // Malaysia
            ['name' => 'Port of Klang', 'code' => 'MYPKG', 'city' => 'Port Klang', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 3.0000, 'longitude' => 101.4000, 'country_code' => 'MYS'],
            ['name' => 'Port of Penang', 'code' => 'MYPEN', 'city' => 'Penang', 'type' => 'Sea', 'size' => 'Medium', 'latitude' => 5.4000, 'longitude' => 100.3000, 'country_code' => 'MYS'],
            
            // China
            ['name' => 'Port of Shanghai', 'code' => 'CNSHA', 'city' => 'Shanghai', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 31.2304, 'longitude' => 121.4737, 'country_code' => 'CHN'],
            ['name' => 'Port of Shenzhen', 'code' => 'CNSNZ', 'city' => 'Shenzhen', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 22.5431, 'longitude' => 114.0579, 'country_code' => 'CHN'],
            
            // USA
            ['name' => 'Port of Los Angeles', 'code' => 'USLAX', 'city' => 'Los Angeles', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 33.7334, 'longitude' => -118.2624, 'country_code' => 'USA'],
            ['name' => 'Port of Long Beach', 'code' => 'USLGB', 'city' => 'Long Beach', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 33.7550, 'longitude' => -118.1900, 'country_code' => 'USA'],
            
            // Japan
            ['name' => 'Port of Tokyo', 'code' => 'JPTYO', 'city' => 'Tokyo', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 35.6510, 'longitude' => 139.7433, 'country_code' => 'JPN'],
            ['name' => 'Port of Yokohama', 'code' => 'JPYOK', 'city' => 'Yokohama', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 35.4500, 'longitude' => 139.6500, 'country_code' => 'JPN'],
            
            // Germany
            ['name' => 'Port of Hamburg', 'code' => 'DEHAM', 'city' => 'Hamburg', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 53.5503, 'longitude' => 9.9946, 'country_code' => 'DEU'],
            
            // Netherlands
            ['name' => 'Port of Rotterdam', 'code' => 'NLRTM', 'city' => 'Rotterdam', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 51.9150, 'longitude' => 4.1604, 'country_code' => 'NLD'],
            
            // UAE
            ['name' => 'Port of Dubai', 'code' => 'AEDXB', 'city' => 'Dubai', 'type' => 'Sea', 'size' => 'Large', 'latitude' => 25.2048, 'longitude' => 55.2708, 'country_code' => 'ARE'],
            
            // Australia
            ['name' => 'Port of Sydney', 'code' => 'AUSYD', 'city' => 'Sydney', 'type' => 'Sea', 'size' => 'Large', 'latitude' => -33.8688, 'longitude' => 151.2093, 'country_code' => 'AUS'],
        ];

        foreach ($ports as $port) {
            $country = Country::where('code', $port['country_code'])->first();
            
            if ($country) {
                Port::updateOrCreate(
                    ['code' => $port['code']],
                    [
                        'name' => $port['name'],
                        'country_id' => $country->id,
                        'city' => $port['city'],
                        'latitude' => $port['latitude'],
                        'longitude' => $port['longitude'],
                        'type' => $port['type'],
                        'size' => $port['size']
                    ]
                );
            }
        }
        
        $this->command->info('✅ Ports seeded successfully!');
    }
}