<?php

namespace App\Providers;

use App\Support\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function () {
            return tap(new Client(), function ($client) {
                $client->setAuthConfig(storage_path('app/client_credentials.json'));
                $client->setAccessType('offline');
                $client->setIncludeGrantedScopes(true);
            });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
