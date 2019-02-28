<?php
/**
 * Part of the GoCardless Pro PHP Client package integration for Laravel
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the MIT License.
 *
 * This source file is subject to the MIT License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Gocardless Laravel
 * @version    0.1.0
 * @author     Nested.net
 * @license    MIT
 * @link       https://nested.net
 */

namespace Nestednet\Gocardless\Laravel;

use GoCardlessPro\Client;
use Illuminate\Support\ServiceProvider;

class GocardlessServiceProvider extends ServiceProvider
{

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/gocardless.php' => config_path('gocardless.php'),
        ], 'config');
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/gocardless.php', 'gocardless');

        $this->registerGocardless();
    }

    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return [
            'gocardless',
        ];
    }

    /**
     * Register the Stripe API class.
     *
     * @return void
     */
    protected function registerGocardless()
    {
        $this->app->singleton('gocardless', function ($app) {
            $config = $app['config']->get('gocardless');
            $token = isset($config['token']) ? $config['token'] : null;
            $environment = isset($config['environment']) ? $config['environment'] : null;
            return new Client( array (
                'access_token' => $token,
                'environment' => $environment
            ));
        });
        $this->app->alias('gocardless', 'GoCardlessPro\Client');
    }
}