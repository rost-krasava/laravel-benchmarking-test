<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use stdClass;

class WeatherService
{
    public function fetchUserWeatherData(string $userId, stdClass $location = null): ?array
    {
        $weatherData = Cache::get('weather_data_' . $userId);
        if (!$weatherData & $location !== null) {
            $apiKey = config('services.openweathermap.api_key');

            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'lat' => $location->latitude,
                'lon' => $location->longitude,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);

            $weatherData = $response->json();

            Cache::put('weather_data_' . $userId, $weatherData, 60);
        }

        return $weatherData ?? null;
    }
}
