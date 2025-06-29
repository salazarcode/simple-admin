<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\ColorSettings;

class ColorThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share active theme colors with all views
        View::composer('*', function ($view) {
            $activeTheme = ColorSettings::getActiveTheme();
            $view->with('activeColors', $activeTheme);
        });
    }
}