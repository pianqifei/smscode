<?php

namespace Pqf\Smscode\Guards;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Pqf\Smscode\Interfaces\Send;
use Pqf\Smscode\SendReturn;

class SubmailGuard implements Send
{
    public function sendSms($phones, $content): SendReturn
    {
        $config = config('sms.guards.submail');
        if(Str::startsWith($phones,'+86')===true){
            $sign = $config['sign'];
        }else{
            $sign = $config['sign_en'];
        }
        if (!Str::contains($content, $sign)) {
            $content=Str::start($content, $sign);
        }
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        if(Str::startsWith($phones,'+86')===true){
            $phones=substr($phones,3);
            $host_url='https://api.mysubmail.com/message/send.json';
        }else{
            $host_url='https://api.mysubmail.com/internationalsms/send.json';
            $data['appid']=$config['appid_international'];
            $data['signature']=$config['signature_international'];
        }
        $data['appid'] =$config['appid'];
        $data['signature'] =$config['signature'];
        $data['to'] = $phones;
        $data['content'] = $content;
        $client = new Client();
        try{
            $response = $client->post($host_url, [
                'timeout' => config('sms.timeout'),
                'http_errors'=>false,
                'json'=>$data,
            ]);
            $ret = json_decode($response->getBody()->getContents(), true);
            if($ret['status']=='success'){
                return new SendReturn($ret['status'] == 'success' ? SendReturn::SUCCESS_CODE : SendReturn::FAIL_CODE, trans('message.send_success'));
            }else{
                Log::info('send_sms_err',['code'=>$ret['code'],'msg'=>$ret['msg']]);
                return new SendReturn(SendReturn::FAIL_CODE ,trans('smscode::sms.send_failed'));
            }
        }catch (\Exception $e){
            Log::info('send_sms_err',['code'=>$e->getCode(),'msg'=>$e->getMessage()]);
            return new SendReturn(SendReturn::FAIL_CODE ,trans('smscode::sms.send_failed'));
        }
    }
}
