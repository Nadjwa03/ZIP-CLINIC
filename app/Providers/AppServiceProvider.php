<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;  // ← TAMBAH INI!
use App\Models\AppSetting;            // ← TAMBAH INI!

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
        // Share settings dengan landing-footer component
        View::composer('components.landing-footer', function ($view) {
            $settings = AppSetting::query()->get()->pluck('value', 'key')->toArray();
            $view->with('settings', $settings);
        });
    }
}