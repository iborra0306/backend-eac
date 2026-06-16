<?php

namespace App\Providers;

use App\Auth\VerifierGuard;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Services\CalificacionService;
use App\Services\HuellaService;
use App\Services\VerifierJwksService;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CalificacionService::class);
        $this->app->singleton(HuellaService::class);
        $this->app->singleton(VerifierJwksService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::extend('verifier', function ($app, $name, array $config) {
            return new VerifierGuard(
                Auth::createUserProvider($config['provider']),
                $app->make(\Illuminate\Http\Request::class),
                $app->make(VerifierJwksService::class)
            );
        });

        // Blade::if define una nueva directiva condicional @role(...) usable en vistas
        Blade::if('role', function (string $role): bool {
            // auth()->check() comprueba que hay un usuario autenticado
            // auth()->user()->hasRole($role) reutiliza el helper definido en User
            return auth()->check() && auth()->user()->hasRole($role);
        });
    }
}
