<?php

namespace App\Providers;

use App\Console\Commands\ServeCommand;
use App\Models\SiteSetting;
use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;
use Illuminate\Support\Facades\View;
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
        // Share site settings (favicon, logo, etc.) with all views so favicon/site title
        // updated in admin applies to website, user dashboard, and admin dashboard.
        View::share('settings', SiteSetting::getSettings());

        // Replace default 'serve' with our version that sets 200M upload limits
        // so release uploads work locally (no run-server.bat needed).
        $this->app->extend(BaseServeCommand::class, function () {
            return $this->app->make(ServeCommand::class);
        });
    }
}
