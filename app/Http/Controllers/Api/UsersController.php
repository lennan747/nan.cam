<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;
use App\Transformers\UserTransformer;
use App\Models\Image;

class UsersController extends Controller
{

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

        //return $this->response->created();
        // 创建成功，返回TOKEN
        return $this->response->item($user,new UserTransformer())->setMeta([
            'access_token' => \Auth::guard('api')->fromUser($user),
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL()*60
        ])->setStatusCode(201);
    }

    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    public function update(UserRequest $userRequest)
    {
        $user = $this->user();

        $attributes = $userRequest->only(['name', 'email', 'introduction']);

        if ($userRequest->avatar_image_id) {
            $image = Image::find($userRequest->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }

        $user->update($attributes);
        return $this->response->item($user, new UserTransformer());
    }
}
