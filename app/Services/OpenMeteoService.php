<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoService extends BaseService
{
    protected $baseUrl = 'https://api.open-meteo.com/v1';
    protected $cacheDuration = 1800; // 30 minutes

    /**
     * Get current weather for coordinates
     */
    public function getCurrentWeather($latitude, $longitude)
    {
        $endpoint = '/forecast';
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current_weather' => true,
            'timezone' => 'auto'
        ];

        return $this->get($endpoint, $params);
    }

    /**
     * Get weather forecast for 7 days
     */
    public function getForecast($latitude, $longitude, $days = 7)
    {
        $endpoint = '/forecast';
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,wind_speed_10m_max,weathercode',
            'timezone' => 'auto',
            'forecast_days' => $days
        ];

        return $this->get($endpoint, $params);
    }

    /**
     * Get weather for multiple locations
     */
    public function getMultipleLocationsWeather($locations)
    {
        $results = [];
        foreach ($locations as $location) {
            $weather = $this->getCurrentWeather($location['lat'], $location['lng']);
            if ($weather) {
                $results[] = [
                    'location' => $location['name'],
                    'weather' => $weather
                ];
            }
        }
        return $results;
    }

    /**
     * Get storm risk for location
     */
    public function getStormRisk($latitude, $longitude)
    {
        $weather = $this->getCurrentWeather($latitude, $longitude);
        
        if (!$weather || !isset($weather['current_weather'])) {
            return ['risk' => 'unknown', 'score' => 0, 'message' => 'Unable to fetch weather data'];
        }

        $windSpeed = $weather['current_weather']['windspeed'] ?? 0;
        $temperature = $weather['current_weather']['temperature'] ?? 0;
        $weatherCode = $weather['current_weather']['weathercode'] ?? 0;

        // Weather code meanings: 0=Clear, 1=Mainly Clear, 2=Partly Cloudy, 3=Overcast, 45=Fog, 48=Depositing Rime Fog
        // 51=Light Drizzle, 53=Moderate Drizzle, 55=Dense Drizzle, 61=Slight Rain, 63=Moderate Rain, 65=Heavy Rain
        // 71=Slight Snow, 73=Moderate Snow, 75=Heavy Snow, 80=Slight Rain Showers, 81=Moderate Rain Showers
        // 82=Violent Rain Showers, 95=Thunderstorm, 96=Thunderstorm with Slight Hail, 99=Thunderstorm with Heavy Hail

        // Calculate risk score
        $score = 0;
        $risks = [];

        // Wind risk
        if ($windSpeed > 50) {
            $score += 40;
            $risks[] = 'Extreme wind';
        } elseif ($windSpeed > 30) {
            $score += 25;
            $risks[] = 'Strong wind';
        } elseif ($windSpeed > 20) {
            $score += 10;
            $risks[] = 'Moderate wind';
        }

        // Rain/Storm risk
        if (in_array($weatherCode, [95, 96, 99])) {
            $score += 40;
            $risks[] = 'Thunderstorm';
        } elseif (in_array($weatherCode, [80, 81, 82])) {
            $score += 30;
            $risks[] = 'Heavy rain showers';
        } elseif (in_array($weatherCode, [61, 63, 65])) {
            $score += 20;
            $risks[] = 'Rain';
        }

        // Snow risk
        if (in_array($weatherCode, [71, 73, 75])) {
            $score += 15;
            $risks[] = 'Snow';
        }

        // Determine risk level
        $risk = 'low';
        if ($score >= 70) {
            $risk = 'critical';
        } elseif ($score >= 50) {
            $risk = 'high';
        } elseif ($score >= 30) {
            $risk = 'medium';
        }

        return [
            'risk' => $risk,
            'score' => $score,
            'wind_speed' => $windSpeed,
            'temperature' => $temperature,
            'weather_code' => $weatherCode,
            'risks' => $risks,
            'message' => empty($risks) ? 'No significant weather risks detected' : implode(', ', $risks)
        ];
    }

    /**
     * Get weather warning for country
     */
    public function getWeatherWarning($latitude, $longitude)
    {
        $stormRisk = $this->getStormRisk($latitude, $longitude);
        
        if ($stormRisk['risk'] === 'critical') {
            return [
                'level' => 'warning',
                'message' => '⚠️ Critical weather conditions detected! ' . $stormRisk['message'],
                'score' => $stormRisk['score']
            ];
        } elseif ($stormRisk['risk'] === 'high') {
            return [
                'level' => 'advisory',
                'message' => '⚠️ High weather risks: ' . $stormRisk['message'],
                'score' => $stormRisk['score']
            ];
        }
        
        return [
            'level' => 'normal',
            'message' => 'Weather conditions are normal',
            'score' => $stormRisk['score']
        ];
    }
}