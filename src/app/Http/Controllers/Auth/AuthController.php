<?php

namespace App\Http\Controllers\Auth;

use App\Repositories\ProviderUserRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AuthController extends Controller
{
    protected AuthFactory $auth;
    protected Request $request;
    protected UserRepositoryInterface $userRepository;
    protected ProviderUserRepositoryInterface $authUserRepository;

    public function __construct(
        AuthFactory $auth,
        Request $request,
        UserRepositoryInterface $userRepository,
        ProviderUserRepositoryInterface $authUserRepository,
    ) {
        $this->auth = $auth;
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->authUserRepository = $authUserRepository;
    }

    public function login()
    {
        if ($this->isLoginMethodToken()) {
            $user = $this->authUserRepository->getByToken($this->request->token);
            $existingUser = $this->userRepository->getByProvider($this->request->provider, $user->id);
            if (!$existingUser) {
                $existingUser = $this->userRepository->createFromAuthProvider($this->request->provider, $user);
            }
        } else {
            $validatedData = $this->request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            $existingUser = $this->userRepository->getByEmail($validatedData['email']);
            if (!$existingUser) {
                $existingUser = $this->userRepository->createFromCredentials(
                    $validatedData['email'],
                    $validatedData['password']
                );
            } else if (!$this->auth->guard()->attempt($validatedData)) {
                return redirect('/')->with('error', 'Wrong authentication credentials');
            }
        }

        $this->auth->guard()->login($existingUser, true);

        return redirect('/home');
    }

    private function isLoginMethodToken(): bool
    {
        return $this->request->has('token') && $this->request->has('provider');
    }

    public function redirect()
    {
        return $this->authUserRepository->getDriver()->stateless()->redirect();
    }

    public function handleCallback()
    {
        try {
            $user = $this->authUserRepository->getUser();
        } catch (\Exception $e) {
            return redirect('/')->with('error', "Authentication failed.");
        }

        return view("auth.handle-{$this->request->provider}-callback", ['token' => $user->token]);
    }

    public function logout()
    {
        $this->auth->guard()->logout();

        $this->request->session()->invalidate();

        $this->request->session()->regenerateToken();

        return redirect('/');
    }
}
