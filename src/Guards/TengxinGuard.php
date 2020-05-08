<?php

namespace Pqf\Smscode\Guards;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Pqf\Smscode\Interfaces\Send;
use Pqf\Smscode\SendReturn;
use GuzzleHttp\Client;

class TengxinGuard implements Send
{
    public function sendSms($phones, $content): SendReturn
    {
        $config = config('sms.guards.tengxin');
        $username = urlencode(Arr::get($config, 'user', ''));
        $password = urlencode(Arr::get($config, 'password', ''));
        $sign = Arr::get($config, 'sign', '');
        $sign = Str::start($sign, '【');
        $sign = Str::finish($sign, '】');
        if (!Str::contains($content, $sign)) {
            $content .= $sign;
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $url = "http://api.1086sms.com/api/sendutf8.aspx?username=$username&password=$password&mobiles=$phones&content=$content";
        $client = new Client();
        try{
            $response = $client->get($url, [
                'timeout' => config('sms.timeout'),
                'http_errors'=>false,
            ])->getBody()->getContents();
            $ret = urldecode($response);
            $result = [];
            foreach (explode('&', $ret) as $v) {
                list($key, $value) = explode('=', $v);
                $result[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
            }
            return new SendReturn($result['result'] == 0 ? SendReturn::SUCCESS_CODE : SendReturn::FAIL_CODE, $result['description']);
        }catch (RequestException $e){
            Log::info('send_sms_err',['code'=>$e->getCode(),'msg'=>$e->getMessage()]);
            return new SendReturn(SendReturn::FAIL_CODE ,trans('smscode::sms.send_failed'));
        }
    }
}
