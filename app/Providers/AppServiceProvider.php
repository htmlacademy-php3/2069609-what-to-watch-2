<?php

namespace App\Providers;

use App\Http\Repositories\Interfaces\MovieRepositoryInterface;
use App\Http\Repositories\OmdbRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // так работает
        $this->app->bind(MovieRepositoryInterface::class, function ($app) {
            return new OmdbRepository($app->make(Client::class));
        });

         // Так не работает!
        /*
        $this->app->bind(
            ClientInterface::class,
            Client::class
        );
        */
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
