<?php

namespace Pqf\Smscode\Guards;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Pqf\Smscode\Interfaces\SmsSend;
use Pqf\Smscode\SendReturn;

class YunpianGuard implements SmsSend
{
    public function sendSms($phones, $content): SendReturn
    {
        $config = config('sms.guards.yunpian');
        $sign = trans('message.'.array_get($config, 'sign', 'light'));
        $sign = str_start($sign, '【');
        $sign = str_finish($sign, '】');
        if (!Str::contains($content, $sign)) {
            $content=Str::start($content,$sign);
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        if(Str::startsWith($phones,'+86')===true){
            $phones=substr($phones,3);
            $host_url='https://sms.yunpian.com';
        }else{
            $host_url='https://us.yunpian.com';
        }
        $client = new Client();
        try{
            $response = $client->post($host_url.'/v2/sms/single_send.json', [
                'timeout' => config('sms.timeout'),
                'http_errors'=>false,
                'form_params' => [
                    'apikey' => array_get($config, 'key', ''),
                    'mobile' => $phones,
                    'text' => $content,
                ],
            ]);
            $ret = json_decode($response->getBody()->getContents(), true);
            if($ret['code']==0){
                return new SendReturn($ret['code'] == 0 ? SendReturn::SUCCESS_CODE : SendReturn::FAIL_CODE, trans('message.send_success'));
            }else{
                Log::info('send_sms_err',['code'=>$ret['code'],'msg'=>$ret['detail']]);
                return new SendReturn(SendReturn::FAIL_CODE ,trans('message.send_failed'));
            }
        }catch (RequestException $e){
            Log::info('send_sms_err',['code'=>$e->getCode(),'msg'=>$e->getMessage()]);
            return new SendReturn(SendReturn::FAIL_CODE ,trans('message.send_failed'));
        }
    }
}
