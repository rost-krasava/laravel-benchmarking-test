<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\HomeController;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Mockery;
use stdClass;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    private const LOCATION = '{"latitude": 1.23, "longitude": 4.56}';
    public function testHome()
    {
        $user = $this->getUser();

        $weatherService = Mockery::mock(WeatherService::class);
        $weatherData = ['main' => ['temp' => 25.5]];
        $weatherService->shouldReceive('fetchUserWeatherData')->with($user->id)->andReturn(null);
        $weatherService->shouldReceive('fetchUserWeatherData')
            ->with($user->id, Mockery::type(stdClass::class))
            ->andReturn($weatherData);

        $user->shouldReceive('update')->once()->with(['location' => self::LOCATION]);
        $sessionManager = Mockery::mock(SessionManager::class);
        $sessionManager->shouldReceive('get')->with('location')->andReturn(self::LOCATION);

        $request = $this->mock(Request::class);
        $request->shouldReceive('user')->andReturn($user);

        $controller = new HomeController($sessionManager, $weatherService);

        $response = $controller->home($request);
        $actualData = json_decode(json_encode($response->getData()), true);

        $this->assertEquals($actualData, [
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
            'main' => ['temp' => 25.5]
        ]);
    }
}
