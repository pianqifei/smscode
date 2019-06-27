<?php

namespace Pqf\Smscode\Guards;

use Pqf\Smscode\Interfaces\SmsSend;
use Pqf\Smscode\SendReturn;

class TengxinGuard implements SmsSend
{
    public function sendSms($phones, $content): SendReturn
    {
        $config = config('sms.guards.tengxin');

        $username = urlencode(array_get($config, 'user', ''));
        $password = urlencode(array_get($config, 'password', ''));
        $sign = array_get($config, 'sign', '');

        $sign = str_start($sign, '【');
        $sign = str_finish($sign, '】');

        if (!str_contains($content, $sign)) {
            $content .= $sign;
        }
        $content = urlencode(iconv("UTF-8", "gb2312//IGNORE", trim($content)));
        $url = "http://api.1086sms.com/api/send.aspx?username=$username&password=$password&mobiles=$phones&content=$content";

        $ret = file_get_contents($url, false, stream_context_create([
            'http' => [
                'timeout' => config('sms.timeout'),
            ],
        ]));
        $ret = urldecode($ret);
        $result = [];
        foreach (explode('&', $ret) as $v) {
            list($key, $value) = explode('=', $v);
            $result[$key] = iconv('gb2312', 'utf-8', $value);
        }
        return new SendReturn($result['result'] == 0 ? SendReturn::SUCCESS_CODE : SendReturn::FAIL_CODE, $result['description']);

    }
}
