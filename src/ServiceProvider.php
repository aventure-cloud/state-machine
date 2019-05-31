<?php

namespace Aventure\StateMachine;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // config
        $this->publishes([
            __DIR__ . '/../config/state-machine.php' => config_path('state-machine.php')
        ], 'config');
    }
	
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // configurations
        $this->mergeConfigFrom(
            __DIR__ . '/../config/state-machine.php', 'state-machine'
        );
    }
}
