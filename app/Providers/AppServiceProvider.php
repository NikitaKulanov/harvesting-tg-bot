<?php

namespace App\Providers;

use App\Services\Telegram\Bot;
use App\Services\Telegram\RequestClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RequestClient::class, function ($app) {
            return new RequestClient(
                httpClient : $this->app->make(Http::class),
                token : config('bot.settings.token')
            );
        });

        $this->app->bind(Bot::class, function ($app) {
            return new Bot(
                config: config('bot'),
                requestClient: $this->app->make(RequestClient::class),
                request: $app['request']
            );
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
