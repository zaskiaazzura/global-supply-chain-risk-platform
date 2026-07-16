<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\OpenMeteoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    protected $openMeteo;

    public function __construct(OpenMeteoService $openMeteo)
    {
        $this->openMeteo = $openMeteo;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = 20;

        $query = Country::whereNotNull('latitude')->whereNotNull('longitude');

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $countries = $query->orderBy('name')->paginate($perPage);
        $totalCountries = Country::whereNotNull('latitude')->whereNotNull('longitude')->count();

        $weatherData = [];
        foreach ($countries as $country) {
            $cacheKey = "weather_{$country->code}";
            $weather = Cache::get($cacheKey);

            if (!$weather) {
                try {
                    $weather = $this->openMeteo->getCurrentWeather(
                        $country->latitude,
                        $country->longitude
                    );
                    Cache::put($cacheKey, $weather, 600);
                } catch (\Exception $e) {
                    $weather = null;
                }
            }

            if ($weather && isset($weather['current_weather'])) {
                $current = $weather['current_weather'];
                $weatherData[] = [
                    'country' => $country,
                    'temperature' => $current['temperature'] ?? null,
                    'windspeed' => $current['windspeed'] ?? null,
                    'weathercode' => $current['weathercode'] ?? null,
                    'risk' => $this->getWeatherRisk($current['windspeed'] ?? 0, $current['weathercode'] ?? 0),
                ];
            } else {
                $weatherData[] = [
                    'country' => $country,
                    'temperature' => null,
                    'windspeed' => null,
                    'weathercode' => null,
                    'risk' => 'unknown',
                ];
            }
        }

        return view('weather.index', compact('weatherData', 'search', 'countries', 'totalCountries'));
    }

    private function getWeatherRisk($windSpeed, $weatherCode)
    {
        $score = 0;
        if ($windSpeed > 50) $score += 40;
        elseif ($windSpeed > 30) $score += 25;
        elseif ($windSpeed > 20) $score += 10;

        if (in_array($weatherCode, [95, 96, 99])) $score += 40;
        elseif (in_array($weatherCode, [80, 81, 82])) $score += 30;
        elseif (in_array($weatherCode, [61, 63, 65])) $score += 20;
        elseif (in_array($weatherCode, [71, 73, 75])) $score += 15;

        if ($score >= 70) return 'critical';
        elseif ($score >= 50) return 'high';
        elseif ($score >= 30) return 'medium';
        return 'low';
    }

    public function refresh()
    {
        $countries = Country::whereNotNull('latitude')->get();
        foreach ($countries as $country) {
            Cache::forget("weather_{$country->code}");
        }

        return redirect()->route('weather.index')
            ->with('success', 'Data cuaca berhasil di-refresh!');
    }
}