<?php

namespace Lance\Cloud;

use Illuminate\Support\ServiceProvider;

class CloudServiceprovider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/cloud.php' => config_path('cloud.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Lance\Cloud\CloudCommunicationContract',
            'Lance\Cloud\EloquentCloudRepository'
        );

        $this->app->singleton('cloud', function() {
            return $this->app->make('Lance\Cloud\CloudHandler');
        });
    }

    public function provides()
    {
        return ['cloud'];
    }
}
