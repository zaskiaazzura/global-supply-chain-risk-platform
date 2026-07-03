<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MarineTrafficService extends BaseService
{
    protected $baseUrl = 'https://api.marinetraffic.com/api/v2';
    protected $cacheDuration = 3600; // 1 hour

    public function __construct()
    {
        $this->apiKey = config('services.marine_traffic.key');
    }

    /**
     * Get real-time ship positions (AIS)
     */
    public function getShipPositions($params = [])
    {
        if (!$this->apiKey) {
            Log::warning('Marine Traffic API key not configured');
            return $this->getDummyShips();
        }

        $endpoint = '/ais/ships';
        $queryParams = array_merge([
            'api_key' => $this->apiKey,
            'format' => 'json',
            'limit' => 100
        ], $params);

        $response = $this->get($endpoint, $queryParams);
        
        if ($response && !isset($response['error_code'])) {
            return $response;
        }

        if ($response && isset($response['error_code'])) {
            Log::error("Marine Traffic API Error: " . $response['error_code']);
        }

        return $this->getDummyShips();
    }

    /**
     * Get ship by MMSI
     */
    public function getShipByMMSI($mmsi)
    {
        if (!$this->apiKey) {
            return null;
        }

        $endpoint = "/ais/ship/{$mmsi}";
        $params = [
            'api_key' => $this->apiKey,
            'format' => 'json'
        ];

        $response = $this->get($endpoint, $params);
        
        if ($response && !isset($response['error_code'])) {
            return $response;
        }

        return null;
    }

    /**
     * Get ships at specific port
     */
    public function getShipsAtPort($portId, $limit = 50)
    {
        if (!$this->apiKey) {
            return $this->getDummyShips();
        }

        $endpoint = "/ais/port/{$portId}";
        $params = [
            'api_key' => $this->apiKey,
            'format' => 'json',
            'limit' => $limit
        ];

        $response = $this->get($endpoint, $params);
        
        if ($response && !isset($response['error_code'])) {
            return $response;
        }

        return $this->getDummyShips();
    }

    /**
     * Get ports list
     */
    public function getPorts($params = [])
    {
        if (!$this->apiKey) {
            return $this->getDummyPorts();
        }

        $endpoint = '/ports';
        $queryParams = array_merge([
            'api_key' => $this->apiKey,
            'format' => 'json',
            'limit' => 100
        ], $params);

        $response = $this->get($endpoint, $queryParams);
        
        if ($response && !isset($response['error_code'])) {
            return $response;
        }

        return $this->getDummyPorts();
    }

    /**
     * Get real-time events at port
     */
    public function getPortEvents($portId, $limit = 20)
    {
        if (!$this->apiKey) {
            return [];
        }

        $endpoint = "/events/port/{$portId}";
        $params = [
            'api_key' => $this->apiKey,
            'format' => 'json',
            'limit' => $limit
        ];

        $response = $this->get($endpoint, $params);
        
        if ($response && !isset($response['error_code'])) {
            return $response;
        }

        return [];
    }

    /**
     * Get ship details from database
     */
    public function getShipDetails($imo)
    {
        if (!$this->apiKey) {
            return null;
        }

        $endpoint = "/ships/{$imo}";
        $params = [
            'api_key' => $this->apiKey,
            'format' => 'json'
        ];

        $response = $this->get($endpoint, $params);
        
        if ($response && !isset($response['error_code'])) {
            return $response;
        }

        return null;
    }

    /**
     * Get vessel photos
     */
    public function getShipPhotos($imo)
    {
        if (!$this->apiKey) {
            return [];
        }

        $endpoint = "/ships/{$imo}/photos";
        $params = [
            'api_key' => $this->apiKey,
            'format' => 'json'
        ];

        $response = $this->get($endpoint, $params);
        
        if ($response && !isset($response['error_code'])) {
            return $response;
        }

        return [];
    }

    /**
     * Get dummy ports data (fallback)
     */
    public function getDummyPorts()
    {
        return [
            ['id' => 1, 'name' => 'Port of Singapore', 'country' => 'Singapore', 'country_code' => 'SG', 'city' => 'Singapore', 'latitude' => 1.290270, 'longitude' => 103.851959, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 2, 'name' => 'Port of Rotterdam', 'country' => 'Netherlands', 'country_code' => 'NL', 'city' => 'Rotterdam', 'latitude' => 51.9150, 'longitude' => 4.1604, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 3, 'name' => 'Port of Shanghai', 'country' => 'China', 'country_code' => 'CN', 'city' => 'Shanghai', 'latitude' => 31.2304, 'longitude' => 121.4737, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 4, 'name' => 'Port of Tanjung Priok', 'country' => 'Indonesia', 'country_code' => 'ID', 'city' => 'Jakarta', 'latitude' => -6.1041, 'longitude' => 106.8778, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 5, 'name' => 'Port of Surabaya', 'country' => 'Indonesia', 'country_code' => 'ID', 'city' => 'Surabaya', 'latitude' => -7.2115, 'longitude' => 112.7205, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 6, 'name' => 'Port of Los Angeles', 'country' => 'United States', 'country_code' => 'US', 'city' => 'Los Angeles', 'latitude' => 33.7334, 'longitude' => -118.2624, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 7, 'name' => 'Port of Hamburg', 'country' => 'Germany', 'country_code' => 'DE', 'city' => 'Hamburg', 'latitude' => 53.5503, 'longitude' => 9.9946, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 8, 'name' => 'Port of Dubai', 'country' => 'United Arab Emirates', 'country_code' => 'AE', 'city' => 'Dubai', 'latitude' => 25.2048, 'longitude' => 55.2708, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 9, 'name' => 'Port of Antwerp', 'country' => 'Belgium', 'country_code' => 'BE', 'city' => 'Antwerp', 'latitude' => 51.2667, 'longitude' => 4.3333, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 10, 'name' => 'Port of Hong Kong', 'country' => 'Hong Kong', 'country_code' => 'HK', 'city' => 'Hong Kong', 'latitude' => 22.3193, 'longitude' => 114.1694, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 11, 'name' => 'Port of Belawan', 'country' => 'Indonesia', 'country_code' => 'ID', 'city' => 'Medan', 'latitude' => 3.7779, 'longitude' => 98.7081, 'type' => 'Sea', 'size' => 'Medium'],
            ['id' => 12, 'name' => 'Port of Makassar', 'country' => 'Indonesia', 'country_code' => 'ID', 'city' => 'Makassar', 'latitude' => -5.1145, 'longitude' => 119.4165, 'type' => 'Sea', 'size' => 'Medium'],
            ['id' => 13, 'name' => 'Port of Balikpapan', 'country' => 'Indonesia', 'country_code' => 'ID', 'city' => 'Balikpapan', 'latitude' => -1.2442, 'longitude' => 116.8621, 'type' => 'Sea', 'size' => 'Medium'],
            ['id' => 14, 'name' => 'Port of Busan', 'country' => 'South Korea', 'country_code' => 'KR', 'city' => 'Busan', 'latitude' => 35.1028, 'longitude' => 129.0240, 'type' => 'Sea', 'size' => 'Large'],
            ['id' => 15, 'name' => 'Port of Tokyo', 'country' => 'Japan', 'country_code' => 'JP', 'city' => 'Tokyo', 'latitude' => 35.6510, 'longitude' => 139.7433, 'type' => 'Sea', 'size' => 'Large']
        ];
    }

    /**
     * Get dummy ships data (fallback)
     */
    public function getDummyShips()
    {
        return [
            [
                'mmsi' => '123456789',
                'imo' => '9876543',
                'name' => 'Container Ship 1',
                'type' => 'Cargo',
                'flag' => 'SG',
                'destination' => 'Singapore',
                'eta' => '2024-12-01 10:00',
                'position' => ['lat' => 1.290270, 'lng' => 103.851959],
                'speed' => 12.5,
                'course' => 45
            ],
            [
                'mmsi' => '987654321',
                'imo' => '1234567',
                'name' => 'Tanker 1',
                'type' => 'Tanker',
                'flag' => 'ID',
                'destination' => 'Jakarta',
                'eta' => '2024-12-02 08:00',
                'position' => ['lat' => -6.1041, 'lng' => 106.8778],
                'speed' => 8.2,
                'course' => 180
            ]
        ];
    }

    /**
     * Sync ports to database
     */
    public function syncPortsToDatabase()
    {
        $ports = $this->getPorts();
        
        if (!$ports) {
            return ['success' => false, 'message' => 'Failed to fetch ports'];
        }

        $count = 0;
        $portData = isset($ports['data']) ? $ports['data'] : $ports;
        
        foreach ($portData as $port) {
            // Find country by code
            $country = \App\Models\Country::where('alpha2', $port['country_code'] ?? '')->first();
            
            if (!$country) {
                continue;
            }

            \App\Models\Port::updateOrCreate(
                ['code' => $port['code'] ?? $port['id']],
                [
                    'name' => $port['name'] ?? null,
                    'country_id' => $country->id,
                    'city' => $port['city'] ?? null,
                    'latitude' => $port['latitude'] ?? null,
                    'longitude' => $port['longitude'] ?? null,
                    'type' => $port['type'] ?? 'Sea',
                    'size' => $port['size'] ?? 'Medium'
                ]
            );
            $count++;
        }

        return ['success' => true, 'count' => $count];
    }
}