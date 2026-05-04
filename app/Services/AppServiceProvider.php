<?php

namespace App\Providers;

use App\Services\CalificacionService;
use App\Services\EACAnalyticsService;
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

        $this->app->singleton(EACAnalyticsService::class, function ($app) {
            return new EACAnalyticsService(
                $app->make(CalificacionService::class)
            );
        });
    }

}
