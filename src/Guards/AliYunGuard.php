<?php

namespace Pqf\Smscode\Guards;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Pqf\Smscode\Interfaces\Send;
use Pqf\Smscode\SendReturn;
use Carbon\Carbon;

class AliYunGuard implements Send
{
    public function sendSms($phones, $content,$type=1): SendReturn
    {
        try{
            $config = config('sms.guards.aliyun');
            $type_arr=$config['Temp_arr'];
            $host_url='dysmsapi.aliyuncs.com';
            $AccessKeySecret=$config['AccessKeySecret'];
            $data['AccessKeyId'] =$config['AccessKeyId'];
            $data['Action']='SendSms';
            $data['Format']='json';
            $data['SignName'] =$config['sign'];
            $data['SignatureMethod'] ='HMAC-SHA1';
            $data['SignatureNonce'] =Str::uuid()->toString();
            $data['SignatureVersion'] ='1.0';
            $data['Timestamp'] =Carbon::now()->subHours(8)->format('Y-m-d\TH:i:s\Z');
            $data['Version'] ='2017-05-25';
            $data['TemplateCode'] =$type_arr[$type];
            $data['PhoneNumbers'] = $phones;
            $data['TemplateParam']=$content;
            ksort($data);
            $query_str=http_build_query($data);
            $un_sign_str='GET&%2F&'.urlencode($query_str);
            $sign_str=urlencode(base64_encode(hash_hmac('sha1',$un_sign_str,$AccessKeySecret.'&',true)));
            $url=$host_url.'/?Signature='.$sign_str.'&'.$query_str;
            $client = new Client();
            $response = $client->get($url, [
                'timeout' => config('sms.timeout'),
                'http_errors'=>false,
            ]);
            $ret = json_decode($response->getBody()->getContents(), true);
            if($ret['Code']=='OK'){
                return new SendReturn($ret['Code'] == 'OK' ? SendReturn::SUCCESS_CODE : SendReturn::FAIL_CODE, trans('message.send_success'));
            }else{
                Log::info('send_sms_err',['code'=>$ret['Code'],'msg'=>$ret['Message']]);
                return new SendReturn(SendReturn::FAIL_CODE ,trans('smscode::sms.send_failed'));
            }
        }catch (\Exception $e){
            Log::info('send_sms_err',['code'=>$e->getCode(),'msg'=>$e->getMessage()]);
            return new SendReturn(SendReturn::FAIL_CODE ,trans('smscode::sms.send_failed'));
        }
    }
}
