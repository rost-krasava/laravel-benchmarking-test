<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle user login using Google SSO.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
        ]);

        $googleUser = Socialite::driver('google')->userFromToken($request->token);
        $existingUser = User::where('email', $googleUser->email)->first();

        if ($existingUser) {
            // User already exists, authenticate the user
            Auth::login($existingUser, true);
            $token = $existingUser->createToken('MyApp')->accessToken;

            return response()->json(['token' => $token]);
        } else {
            // User doesn't exist, return an error
            return response()->json(['error' => 'User not found.'], 404);
        }
    }

    public function register()
    {

    }
}
