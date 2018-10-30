<?php
namespace Revenuefm\Ssoguard;

use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Revenuefm\Ssoguard\Guards\SsoGuard;

/**
 * Created by PhpStorm.
 * User: gorankrgovic
 * Date: 10/30/18
 * Time: 9:00 AM
 */

/**
 * Class SsoguardServiceProvider
 *
 * @package Revenuefm\Ssoguard
 */
class SsoguardServiceProvider extends ServiceProvider
{


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGuard();
        $this->offerPublishing();
    }


    /**
     * Register the guard.
     *
     * @return void
     */
    protected function registerGuard()
    {
        Auth::extend('ssoguard', function ($app, $name, array $config) {
            return tap($this->makeGuard($config), function ($guard) {
                $this->app->refresh('request', $guard, 'setRequest');
            });
        });
    }

    /**
     * Make an instance of the guard.
     *
     * @param  array  $config
     * @return \Illuminate\Auth\RequestGuard
     */
    protected function makeGuard(array $config)
    {
        return new RequestGuard(function ($request) use ($config) {
            return (new SsoGuard(
                Auth::createUserProvider($config['provider'])
            ))->user($request);
        }, $this->app['request']);
    }

    /**
     * Setup the resource publishing groups for Passport.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ssoguard.php' => config_path('ssoguard.php'),
            ], 'ssoguard-config');
        }
    }

}
