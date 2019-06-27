<?php

return [
    'sms_username' => env('SMS_USERNAME', ''),
    'sms_password' => env('SMS_PASSWORD', ''),
    'sms_sign' => env('SMS_SIGN', ''),
    'default' => env('SMS', 'tengxin'),
    'ip_day_limit' => env('SMS_IP_DAY_LIMIT', 20), //每ip每天限制多少条短信
    'phone_hour_limit' => env('SMS_PHONE_HOUR_LIMIT', 10), //每个电话号码每小时限制条数
    'timeout' => env('SMS_TIMEOUT', 5),

    'guards' => [
        'tengxin' => [
            'user' => env('SMS_USERNAME', ''),
            'password' => env('SMS_PASSWORD', ''),
            'sign' => env('SMS_SIGN', ''),
        ],
        'lexin' => [
            'user' => env('SMS_USERNAME', ''),
            'password' => env('SMS_PASSWORD', ''),
            'sign' => env('SMS_SIGN', ''),
        ],
        'yunpian' => [
            'user' => env('SMS_USERNAME', ''),
            'password' => env('SMS_PASSWORD', ''),
            'sign' => env('SMS_SIGN', ''),
            'access_key' => env('SMS_KEY', ''),
        ],
    ],
];
