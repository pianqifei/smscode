<?php

namespace Pqf\Smscode\Guards;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Pqf\Smscode\Interfaces\Send;
use Pqf\Smscode\SendReturn;

class YunpianGuard implements Send
{
    public function sendSms($phones, $content): SendReturn
    {
        $config = config('sms.guards.yunpian');
        if(Str::startsWith($phones,'+86')===true){
            $sign = $config['sign'];
        }else{
            $sign = $config['sign_en'];
        }
        $sign = Str::start($sign, '【');
        $sign = Str::finish($sign, '】');
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
                    'apikey' => Arr::get($config, 'access_key', ''),
                    'mobile' => $phones,
                    'text' => $content,
                ],
            ]);
            $ret = json_decode($response->getBody()->getContents(), true);
            if($ret['code']==0){
                return new SendReturn($ret['code'] == 0 ? SendReturn::SUCCESS_CODE : SendReturn::FAIL_CODE, trans('smscode::sms.send_success'));
            }else{
                Log::info('send_sms_err',['code'=>$ret['code'],'msg'=>$ret['detail']]);
                return new SendReturn(SendReturn::FAIL_CODE ,trans('smscode::sms.send_failed'));
            }
        }catch (RequestException $e){
            Log::info('send_sms_err',['code'=>$e->getCode(),'msg'=>$e->getMessage()]);
            return new SendReturn(SendReturn::FAIL_CODE ,trans('smscode::sms.send_failed'));
        }
    }
}
