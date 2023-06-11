<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\ProviderUserRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected UserRepositoryInterface $userRepository;
    protected ProviderUserRepositoryInterface $authUserRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ProviderUserRepositoryInterface $authUserRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->authUserRepository = $authUserRepository;
    }

    public function login(Request $request)
    {
        if ($this->isLoginMethodToken($request)) {
            $user = $this->authUserRepository->getByToken($request->token);
            $existingUser = $this->userRepository->getByProvider($request->provider, $user->id);
            if (!$existingUser) {
                $existingUser = $this->userRepository->createFromAuthProvider($request->provider, $user);
            }
        } else {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            $existingUser = $this->userRepository->getByEmail($validatedData['email']);
            if (!$existingUser) {
                $existingUser = $this->userRepository->createFromCredentials(
                    $validatedData['email'],
                    $validatedData['password']
                );
            } else if (!Auth::attempt($validatedData)) {
                return redirect('/')->with('error', 'Wrong authentication credentials');
            }
        }

        Auth::login($existingUser, true);

        return redirect('/home');
    }

    private function isLoginMethodToken(Request $request): bool
    {
        return $request->has('token') && $request->has('provider');
    }

    public function redirect(Request $request)
    {
        return $this->authUserRepository->getDriver()->stateless()->redirect();
    }

    public function handleCallback(Request $request)
    {
        try {
            $user = $this->authUserRepository->getUser();
        } catch (\Exception $e) {
            return redirect('/')->with('error', "Authentication failed.");
        }

        return view("auth.handle-{$request->provider}-callback", ['token' => $user->token]);
    }

    public function logout(Request $request)
    {
        auth()->guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
