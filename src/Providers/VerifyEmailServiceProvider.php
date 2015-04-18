<?php
namespace Taketwo\Providers;

use Taketwo\Foundation\DatabaseTokenRepository;
use Taketwo\Foundation\VerifyEmailBroker;
use Illuminate\Support\ServiceProvider;

class VerifyEmailServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerVerifyEmailBroker();
        $this->registerTokenRepository();
    }

    /**
     * Register the password broker instance.
     *
     * @return void
     */
    protected function registerVerifyEmailBroker()
    {
        $this->app->singleton('verify.email', function ($app) {
            $tokens = $app['verify.email.tokens'];
            $users = $app['auth']->driver()->getProvider();
            $view = $app['config']['auth.email_verify.email'];

            return new VerifyEmailBroker($tokens, $users, $app['mailer'], $view);
        });
    }

    /**
     * Register the token repository implementation.
     *
     * @return void
     */
    protected function registerTokenRepository()
    {
        $this->app->singleton('verify.email.tokens', function ($app) {
            $connection = $app['db']->connection();
            $table = $app['config']['auth.email_verify.table'];
            $key = $app['config']['app.key'];
            $expire = $app['config']->get('auth.email_verify.expire', 60);

            return new DatabaseTokenRepository($connection, $table, $key, $expire);
        });
    }

    public function provides()
    {
        return ['verify.email', 'verify.email.tokens'];
    }
}
