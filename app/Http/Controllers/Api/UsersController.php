<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;

class UsersController extends Controller
{
    //
    public function store(UserRequest $userRequest)
    {
        $verifyData = \Cache::get($userRequest->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $userRequest->verification_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $userRequest->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($userRequest->password),
        ]);

        \Cache::forget($userRequest->verification_key);

        return $this->response->created();
    }
}
