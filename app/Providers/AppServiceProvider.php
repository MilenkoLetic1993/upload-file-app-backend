<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Services\Csv\Sources\Kaggle;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //If we add more sources, we need to bind their implementations here
        $this->app->bind('csvFileReader.source.kaggle', function () {
            return new Kaggle();
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
