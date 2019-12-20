<?php

namespace Pqf\Smscode\Facades;

use Illuminate\Support\Facades\Facade;

class SmsCode extends Facade
{
    /**
     * Return the facade accessor.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'smscode';
    }
}
