<?php

namespace YenaingtunDev\MMQR;

use Illuminate\Support\ServiceProvider;

class MMQRServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/mmqr.php',
            'mmqr'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/mmqr.php' => config_path('mmqr.php'),
            ], 'mmqr-config');
        }
    }
}
