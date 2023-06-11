<?php

namespace App\Providers;

use App\Http\Controllers\Auth\GoogleController;
use App\Repositories\EloquentUserRepository;
use App\Repositories\GoogleUserRepository;
use App\Repositories\ProviderUserRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\SocialiteServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(SocialiteServiceProvider::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(ProviderUserRepositoryInterface::class, GoogleUserRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
