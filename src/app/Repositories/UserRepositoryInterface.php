<?php

namespace App\Repositories;

use Laravel\Socialite\Two\User as SocialiteUser;

interface UserRepositoryInterface
{
    public function createFromAuthProvider(string $provider, SocialiteUser $user);

    public function createFromCredentials(
        string $email,
        string $password
    );

    public function getByProvider(string $provider, string $providerId);

    public function getByEmail(string $email);
}
