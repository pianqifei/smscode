<?php

namespace Pqf\Smscode\Guards;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Pqf\Smscode\Interfaces\Send;
use Pqf\Smscode\SendReturn;

class LexinGuard implements Send
{
    public function sendSms($phones, $content): SendReturn
    {
        $config = config('sms.guards.lexin');
        $username = Arr::get($config, 'user', '');
        $password = Arr::get($config, 'password', '');
        $password = strtoupper(md5($password));
        if(Str::startsWith($phones,'+86')===true){
            $sign = $config['sign'];
        }else{
            $sign = $config['sign_en'];
        }
        if (!Str::contains($content, $sign)) {
            $content .= $sign;
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $bizId = date('YmdHis');
        $client = new Client();
        try{
            $response = $client->post('http://sdk.lx198.com/sdk/send', [
                'timeout' => config('sms.timeout'),
                'http_errors'=>false,
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
        }catch (RequestException $e){
            Log::info('send_sms_err',['code'=>$e->getCode(),'msg'=>$e->getMessage()]);
            return new SendReturn(SendReturn::FAIL_CODE ,trans('smscode::sms.send_failed'));
        }

    }
}
