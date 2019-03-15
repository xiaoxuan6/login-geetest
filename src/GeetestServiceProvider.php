<?php

namespace James\Geetest;

use Illuminate\Support\ServiceProvider;

class GeetestServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'geetest');

        if ($this->app->runningInConsole() ) {
            $this->publishes([__DIR__ . '/../resources/assets' => public_path('vendor/laravel-admin/geetest')], 'geetest');
        }
        $this->publishes([__DIR__ . '/../config/geetest.php' => config_path('geetest.php')], 'geetest');

        Geetest::boot();
    }
}