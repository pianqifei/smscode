<?php

namespace Pqf\Smscode;

use Illuminate\Support\ServiceProvider;
use Pqf\Smscode\Sms;

class SmsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')]);
        $this->publishes([__DIR__.'/../config/sms.php' => config_path('sms.php')]);
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'sms');
        $this->publishes([__DIR__.'/resources/lang' => resource_path('lang/vendor/sms')],'sms-lang');

    }

    public function register()
    {
        $this->app->singleton('smscode', function () {
            return new Sms();
        });
    }
}