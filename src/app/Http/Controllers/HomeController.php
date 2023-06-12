<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use App\Services\WeatherService;
use Illuminate\Session\SessionManager;

class HomeController extends Controller
{
    private SessionManager $sessionManager;
    private WeatherService $weatherService;

    public function __construct(
        SessionManager $sessionManager,
        WeatherService $weatherService
    ) {
        $this->sessionManager = $sessionManager;
        $this->weatherService = $weatherService;
    }

    public function home(Request $request): JsonResponse
    {
        $user = $request->user();
        $weatherData = $this->weatherService->fetchUserWeatherData($user->id);
        if (!$weatherData) {
            $location = $this->sessionManager->get('location');
            if (!$location) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tracking user location is unavailable',
                ]);
            }

            $user->update(['location' => $location]);

            try {
                $weatherData = $this->weatherService->fetchUserWeatherData($user->id, json_decode($location));
            } catch (Throwable $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fetching user location weather data is unavailable',
                    'json' => json_encode($e),
                ]);
            }
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
}
