<?php

namespace Tests\Unit;

use App\Http\Controllers\Auth\AuthController;
use App\Repositories\ProviderUserRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Mockery;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    private const AUTH_DATA = [
        'token' => 'dummy_token',
        'provider' => 'google',
    ];
    private const CREDENTIALS = [
        'email' => 'john@example.com',
        'password' => 'password123',
    ];

    private AuthFactory $authFactory;
    private Request $request;
    private UserRepositoryInterface $userRepository;
    private ProviderUserRepositoryInterface $authUserRepository;
    private Redirector $redirector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authFactory = Mockery::mock(AuthFactory::class);
        $this->request = Mockery::mock(Request::class);
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->authUserRepository = Mockery::mock(ProviderUserRepositoryInterface::class);
        $this->redirector = Mockery::mock(Redirector::class);
    }

    private function createAuthController(): AuthController
    {
        return new AuthController(
            $this->authFactory,
            $this->request,
            $this->userRepository,
            $this->authUserRepository
        );
    }

    private function setTokenLoginMocks(
        bool $findExistingUser = false,
        bool $createNewUser = false
    ) {
        $this->request->shouldReceive('has')->with('token')->andReturn(true);
        $this->request->shouldReceive('has')->with('provider')->andReturn(true);
        $this->request->token = self::AUTH_DATA['token'];
        $this->request->provider = self::AUTH_DATA['provider'];

        $user = $this->getUser();
        $authUser = $this->getAuthUser();

        $this->authUserRepository->shouldReceive('getByToken')
            ->with(self::AUTH_DATA['token'])
            ->andReturn($authUser);
        $this->userRepository->shouldReceive('getByProvider')
            ->with(self::AUTH_DATA['provider'], $authUser->id)
            ->andReturn($findExistingUser ? $user : null);
        $this->userRepository->shouldReceive('createFromAuthProvider')
            ->with(self::AUTH_DATA['provider'], $authUser)
            ->andReturn($createNewUser ? $user : null);

        $this->authFactory->shouldReceive('guard')->andReturnSelf();
        $this->authFactory->shouldReceive('login')->with($user, true);

        $this->redirector->shouldReceive('to')
            ->with('/home')
            ->andReturn(Mockery::mock(RedirectResponse::class));
    }

    private function setCredentialsLoginMocks(
        bool $findExistingUser = false,
        bool $withValidCredentials = true,
        bool $createNewUser = false
    ) {
        $this->request->shouldReceive('has')->with('token')->andReturn(false);
        $this->request->shouldReceive('validate')->with([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ])->andReturn(self::CREDENTIALS);

        $user = $this->getUser();
        $this->userRepository->shouldReceive('getByEmail')
            ->with(self::CREDENTIALS['email'])
            ->andReturn($findExistingUser ? $user : null);

        if ($createNewUser) {
            $this->userRepository->shouldReceive('createFromCredentials')
                ->with(self::CREDENTIALS['email'], self::CREDENTIALS['password'])
                ->andReturn($user);
        }

        $guard = Mockery::mock(Guard::class);
        if ($withValidCredentials) {
            $guard->shouldReceive('attempt')
                ->with(self::CREDENTIALS)
                ->andReturn(true);

            $this->authFactory->shouldReceive('guard')->andReturn($guard);

            $this->redirector->shouldReceive('to')
                ->with('/home')
                ->andReturn(Mockery::mock(RedirectResponse::class));
        }

        $guard->shouldReceive('login')->with($user, true);
        $this->authFactory->shouldReceive('guard')->andReturn($guard);

        $this->redirector->shouldReceive('to')
            ->with('/')
            ->andReturn(Mockery::mock(RedirectResponse::class));
    }

    private function assertRedirectedHome(RedirectResponse|Redirector $response)
    {
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertArrayHasKey('location', $response->headers->all());
        $this->assertStringContainsString('/home', $response->headers->get('location'));
    }

    public function testLoginWithTokenShouldLoginUser()
    {
        $this->setTokenLoginMocks(true, true, false);

        $controller = $this->createAuthController();
        $response = $controller->login();

        $this->assertRedirectedHome($response);
    }

    public function testLoginWithTokenShouldRegisterUser()
    {
        $this->setTokenLoginMocks(false, true, true);

        $controller = $this->createAuthController();
        $response = $controller->login();

        $this->assertRedirectedHome($response);
    }

    public function testLoginWithCredentialsShouldLoginUser()
    {
        $this->setCredentialsLoginMocks(true,true, false);

        $controller = $this->createAuthController();
        $response = $controller->login();

        $this->assertRedirectedHome($response);
    }

    public function testLoginWithCredentialsShouldRegisterUser()
    {
        $this->setCredentialsLoginMocks(false,true, true);

        $controller = $this->createAuthController();
        $response = $controller->login();

        $this->assertRedirectedHome($response);
    }
}
