<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
     }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Implicitly grant "SuperAdmin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole(\App\Enums\UserRoleEnum::SuperAdmin->value) ? true : null;
        });

        // Register event listeners
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\AdminCreated::class,
            \App\Listeners\LogAction::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\AdminCreated::class,
            \App\Listeners\SendNotification::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\EmployeeCreated::class,
            \App\Listeners\LogAction::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\PayoutCreated::class,
            \App\Listeners\LogAction::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\PayoutCreated::class,
            \App\Listeners\SendNotification::class
        );
    }
}
