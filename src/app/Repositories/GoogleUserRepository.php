<?php

namespace App\Repositories;

use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

class GoogleUserRepository implements ProviderUserRepositoryInterface
{
    public function getDriver(): Provider
    {
        return Socialite::driver('google');
    }

    public function getUser(): User
    {
        return $this->getDriver()->stateless()->user();
    }

    public function getByToken(string $token): User
    {
        return $this->getDriver()->userFromToken($token);
    }
}
