<?php

namespace Pqf\Smscode\Guards;

use GuzzleHttp\Client;
use Pqf\Smscode\Interfaces\SmsSend;
use Pqf\Smscode\SendReturn;

class LexinGuard implements SmsSend
{
    public function sendSms($phones, $content): SendReturn
    {
        $config = config('sms.guards.lexin');

        $username = array_get($config, 'user', '');
        $password = array_get($config, 'password', '');
        $password = strtoupper(md5($password));
        $sign = array_get($config, 'sign', '');

        $sign = str_start($sign, '【');
        $sign = str_finish($sign, '】');

        if (!str_contains($content, $sign)) {
            $content .= $sign;
        }

        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $bizId = date('YmdHis');
        // $url = "http: //sdk.lx198.com/sdk/send?accName=$username&accPwd=$password&aimcodes=$phones&content=$content&bizId=$bizId&dataType=json";

        $client = new Client();
        $response = $client->post('http://sdk.lx198.com/sdk/send', [
            'timeout' => config('sms.timeout'),
            'form_params' => [
                'accName' => $username,
                'accPwd' => $password,
                'aimcodes' => $phones,
                'content' => $content,
                'bizId' => $bizId,
                'dataType' => 'json',
            ],
        ]);
        $ret = json_decode($response->getBody()->getContents(), true);
        return new SendReturn($ret['replyCode'] == 1 ? SendReturn::SUCCESS_CODE : SendReturn::FAIL_CODE, $ret['replyMsg']);

    }
}
