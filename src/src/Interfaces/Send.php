<?php

namespace Pqf\Smscode\Interfaces;

use Pqf\Smscode\SendReturn;

interface Send
{
    public function sendSms($phones, $content): SendReturn;
}
