<?php
namespace Taketwo\Providers;

use Illuminate\Auth\Guard;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \Auth::extend('taketwo', function ($app) {
            $provider = new TakeTwoUserProvider($app['hash']);
            return new Guard($provider, $app['session.store']);
        });
    }

    public function register()
    {
        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'Taketwo\Services\Registrar'
        );
    }
}
