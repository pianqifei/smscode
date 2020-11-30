<?php

return [
    'sms_username' => env('SMS_USERNAME', ''),
    'sms_password' => env('SMS_PASSWORD', ''),
    'default' => env('SMS', 'aliyun'),
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
        'submail' => [
            'appid' => env('SMS_APPID', ''),
            'signature' => env('SMS_SIGNATURE', ''),
            'appid_international' => env('SMS_APPID_INTERNATIONAL', ''),
            'signature_international' => env('SMS_SIGNATURE_INTERNATIONAL', ''),
            'sign' => env('SMS_SIGN', ''),
        ],
        'aliyun' => [
            'AccessKeyId' => env('SMS_KEY_ID', ''),
            'AccessKeySecret' => env('SMS_KEY_SECRET', ''),
            'TemplateCode' => env('SMS_TEMPLATE_CODE', ''),
            'sign' => env('SMS_SIGN', ''),
            'Temp_arr'=>[
                1=>'SMS_206000202',
                2=>'SMS_206000201',
                3=>'SMS_206000200',
            ]
        ],
    ],
];
