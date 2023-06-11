<?php

namespace App\Repositories;

use App\Models\User;
use Laravel\Socialite\Two\User as SocialiteUser;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function getByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function getByProvider(string $provider, string $providerId)
    {
        return User::where([
            'provider' => $provider,
            'provider_id' => $providerId,
        ])->first();
    }

    public function createFromCredentials(
        string $email,
        string $password
    ) {
        return User::create([
            'name' => '',
            'email' => $email,
            'password' => Hash::make($password)
        ]);
    }

    public function createFromAuthProvider(string $provider, SocialiteUser $user)
    {
        $firstName = $user->user['given_name'];
        $lastName = $user->user['family_name'];

        return User::create([
            'name' => $this->concatUserName($firstName, $lastName),
            'email' => $user->email,
            'password' => Hash::make($user->token),
            'profile' => $profile ?? '',
            'first_name' => $firstName ?? '',
            'last_name' => $lastName ?? '',
            'status' => User::STATUS_ACTIVE,
            'provider' => $provider,
            'provider_id' => $user->id,
        ]);
    }

    private function concatUserName($firstName, $lastName): string
    {
        return $firstName ? "$firstName " : "" . $lastName ?? "";
    }
}
