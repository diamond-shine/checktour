<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('Bookeo', function () {
            return new \App\Services\Bookeo(
                config('services.bookeo.api_key'),
                config('services.bookeo.secret_key')
            );
        });
    }
}
