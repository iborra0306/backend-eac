<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GrafoService;
use App\Services\RecomendacionService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GrafoService::class);

        $this->app->singleton(RecomendacionService::class, function ($app) {
            return new RecomendacionService($app->make(GrafoService::class));
        });
    }

}
