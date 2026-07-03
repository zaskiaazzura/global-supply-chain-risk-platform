<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OpenMeteoService;
use App\Services\RestCountriesService;
use App\Services\ExchangeRateService;
use App\Services\GNewsService;
use App\Services\WorldBankService;
use App\Services\MarineTrafficService;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OpenMeteoService::class, function ($app) {
            return new OpenMeteoService();
        });

        $this->app->singleton(RestCountriesService::class, function ($app) {
            return new RestCountriesService();
        });

        $this->app->singleton(ExchangeRateService::class, function ($app) {
            return new ExchangeRateService();
        });

        $this->app->singleton(GNewsService::class, function ($app) {
            return new GNewsService();
        });

        $this->app->singleton(WorldBankService::class, function ($app) {
            return new WorldBankService();
        });

        $this->app->singleton(MarineTrafficService::class, function ($app) {
            return new MarineTrafficService();
        });
    }

    public function boot()
    {
        
    }
}