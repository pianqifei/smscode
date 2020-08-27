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
        $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang/vendor/sms')]);
        $this->loadTranslationsFrom(resource_path('lang/vendor/sms'), 'smscode');
    }

    public function register()
    {
        $this->app->singleton('smscode', function () {
            return new Sms();
        });
    }
}