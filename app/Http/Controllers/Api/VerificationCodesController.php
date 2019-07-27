<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use App\Http\Requests\Api\VerificationCodeRequest;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;

        if (!app()->environment('production')) {
            $code = '6789';
        } else {
            //str_pad 将字符串填充到指定长度
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            try {
                $result = $easySms->send($phone, [
                    'content' => "您正在申请手机注册，验证码为：" . $code . "，6分钟内有效！",
                    'template' => 'SMS_171355622',
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                return $this->response->errorInternal($message ?: '短信发送异常');
            }
        }


        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinutes(10);
        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        /*
         * 在使用model模型获取数据得时候，$user->topics 返回的是数据集合, $user->topics()返回的是数据库查询语句
         */
        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
