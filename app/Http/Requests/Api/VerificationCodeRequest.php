<?php

namespace App\Http\Requests\Api;

//use Illuminate\Foundation\Http\FormRequest;
// FormRequest 是 DingoApi 为我们提供的基类。
//use Dingo\Api\Http\FormRequest;

class VerificationCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'phone' => [
                'captcha_key' => 'required|string',
                'captcha_code' => 'required|string',
            ]
        ];
    }

    public function attributes()
    {
        return [
            'captcha_key' => '图片验证码 key',
            'captcha_code' => '图片验证码',
        ];
    }
}
