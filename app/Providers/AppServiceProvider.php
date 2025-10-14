<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AccurateService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
     public function register(): void
    {
      
        $this->app->singleton(AccurateService::class, function ($app) {
            return new AccurateService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
