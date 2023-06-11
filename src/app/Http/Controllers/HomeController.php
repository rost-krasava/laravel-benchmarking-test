<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use stdClass;
use Throwable;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $user = $request->user();
        $weatherData = Cache::get('weather_data_' . $user->id);
        if (!$weatherData) {
            $location = Session::get('location');
            if (!$location) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tracking user location is unavailable',
                ]);
            }

            $user->update(['location' => $location]);

            try {
                $weatherData = $this->fetchWeatherData(json_decode($location));
            } catch (Throwable $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fetching user location weather data is unavailable',
                    'json' => json_encode($e),
                ]);
            }

            Cache::put('weather_data_' . $user->id, $weatherData, 60);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'profile' => $user->profile,
                'status' => $user->getStatus(),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'main' => $weatherData['main']
        ]);
    }

    private function fetchWeatherData(stdClass $location): array
    {
        $apiKey = config('services.openweathermap.api_key');

        $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $location->latitude,
            'lon' => $location->longitude,
            'appid' => $apiKey,
            'units' => 'metric',
        ]);

        return $response->json();
    }
}
