<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    /**
    * Redirect the user to the Google authentication page.
    *
    * @return \Illuminate\Http\RedirectResponse
    */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback()
    {
        try {
//            $user = Socialite::driver('google')->user();
            $user = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            throw $e;
//            return response()->json(['error' => 'Google authentication failed.'], 401);
        }
        dd($user);

        // Check if the user exists in the database
        $existingUser = User::where('email', $user->email)->first();
        if ($existingUser) {
            // User already exists, authenticate the user
            Auth::login($existingUser, true);
            $token = $existingUser->createToken('MyApp')->accessToken;

            return response()->json(['token' => $token]);
        } else {
            // User doesn't exist, create a new user
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => bcrypt('your-default-password'), // You can generate a random password here
            ]);

            Auth::login($newUser, true);
            $token = $newUser->createToken('MyApp')->accessToken;

            // view -> home
//            return response()->json(['token' => $token]);
            return redirect('/home')->with(['token' => $token]); // Перенаправление после аутентификации
        }
    }
}
