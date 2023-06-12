<?php

namespace Tests;

use App\Models\User;
use Laravel\Socialite\Two\User as SocialiteUser;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getUser(): User
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn("123");
        $user->shouldReceive('getAttribute')->with('first_name')->andReturn('John');
        $user->shouldReceive('getAttribute')->with('last_name')->andReturn('Doe');
        $user->shouldReceive('getAttribute')->with('email')->andReturn('john@example.com');
        $user->shouldReceive('getAttribute')->with('password')->andReturn('password123');
        $user->shouldReceive('getAttribute')->with('profile')->andReturn('profile_data');
        $user->shouldReceive('getStatus')->andReturn('active');
        $user->shouldReceive('getAttribute')->with('created_at')->andReturn('2023-01-01 00:00:00');
        $user->shouldReceive('getAttribute')->with('updated_at')->andReturn('2023-01-02 00:00:00');

        return $user;
    }

    protected function getAuthUser(): SocialiteUser
    {
        $authUser = Mockery::mock(SocialiteUser::class);
        $authUser->id = "321";
//        $authUser->shouldReceive('getAttribute')->with('id')->andReturn("321");

        return $authUser;
    }
}
