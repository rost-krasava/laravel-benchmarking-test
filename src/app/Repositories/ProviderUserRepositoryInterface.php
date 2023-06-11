<?php

namespace App\Repositories;

use Laravel\Socialite\Contracts\Provider;

interface ProviderUserRepositoryInterface
{
    public function getDriver(): Provider;
    public function getUser();

    public function getByToken(string $token);
}
