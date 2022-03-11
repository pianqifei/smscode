<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/26
 * Time: 17:32
 */

namespace Pqf\Smscode;

use Illuminate\Support\Str;
use Pqf\Smscode\Models\SmsCode;
use Carbon\Carbon;
use Exception;
use Pqf\Smscode\Interfaces\Send;

class Sms
{
    public function sendSms($phones, $content)
    {
        $smsGuard = config('sms.default');
        $smsGuardClass = "Pqf\\Smscode\\Guards\\" . ucfirst($smsGuard) . 'Guard';
        $guardInstance = new $smsGuardClass;
        if ($guardInstance instanceof Send) {
            return $guardInstance->sendSms($phones, $content);
        }
        throw new Exception("sms guard must implements SmsSend interface", 1);
    }

    public function checkSmsCode($phone, $code,$type=1)
    {
        $expire=config('sms.timeout')*60;
        $sms_code = SmsCode::where('phone', $phone)
            ->where('code', $code)
            //->where('status', SmsCode::STATUS_UNUSED)
            ->where('result',SendReturn::SUCCESS_CODE)
            ->where('type',$type)
            ->orderBy('id', 'desc')->first();
        if (!$sms_code || time() - $sms_code->created_at->timestamp > $expire) {
            return ['message' =>trans('smscode::sms.sms_err')];
        }
        //有效时间内验证码可复用
       /* $sms_code->status = SmsCode::STATUS_USED;
        $sms_code->save();*/

        return true;
    }
    //发送验证码并保存数据库
    public function sendCodeAndSave($phone,$type=1)
    {
        $dayCount = SmsCode::where('phone', $phone)->whereDate('created_at', Carbon::now()->toDateString())->where('ip', request()->ip())->where('type',$type)->count();
        if ($dayCount > config('sms.ip_day_limit')) {
            return [
                'success' => false,
                'message' => trans('smscode::sms.sms_limit'),
            ];
        }
        $phoneCount = SmsCode::where('phone', $phone)->where('created_at', '>', Carbon::now()->subHours(1))->where('type',$type)->count();
        if ($phoneCount > config('sms.phone_hour_limit')) {
            return [
                'success' => false,
                'message' => trans('smscode::sms.sms_limit'),
            ];
        }
        $code = mt_rand(100000, 999999);
        if(config('sms.default')=='aliyun'){
            $content=json_encode(['code'=>$code]);
        }else{
            if(Str::startsWith($phone,'+86')===true){
                $content = trans('smscode::sms.sms_temp',['code'=>$code,'minutes'=>config('sms.timeout')],'zh_CN');
            }else{
                $content = trans('smscode::sms.sms_temp',['code'=>$code,'minutes'=>config('sms.timeout')],'en');
            }
        }
        $sms_code = new SmsCode;
        $sms_code->ip = request()->ip();
        $sms_code->phone = $phone;
        $sms_code->code = $code;
        $sms_code->type = $type;
        $sms_code->save();
        $res = $this->sendSms($phone, $content,$type);
        $sms_code->result = $res->success;
        $sms_code->save();
        if ($res->success == SendReturn::SUCCESS_CODE) {
            return [
                'success' => true,
                'message' => trans('smscode::sms.send_success'),
            ];
        }

        return [
            'success' => false,
            'message' =>trans('smscode::sms.send_failed'),
            //'message' =>$res->result,
        ];
    }
}