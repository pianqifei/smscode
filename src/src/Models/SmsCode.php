<?php

namespace Pqf\Smscode\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCode extends Model
{
    const STATUS_UNUSED = 0;
    const STATUS_USED = 1;

    protected $table = 'sms_code';
}