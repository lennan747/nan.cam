<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

// Dingo Api
$api = app('Dingo\Api\Routing\Router');

//$api->version('v1', function($api) {
//    $api->get('version', function() {
//        return response('this is version v1');
//    });
//});
//
//$api->version('v2', function($api) {
//    $api->get('version', function() {
//        return response('this is version v2');
//    });
//});

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => 'serializer:array'
], function ($api) {
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function ($api) {
        // 生成，发送验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')->name('api.verificationCodes.store');
        // 注册用户
        $api->post('users', 'UsersController@store')->name('api.users.store');
        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')->name('api.captchas.store');
        // 第三方登录
        $api->post('socials/{social_type}/authorizations','AuthorizationsController@socialStore')->name('api.socials.authorizations.store');
        // 登录
        $api->post('authorizations','AuthorizationsController@store')->name('api.authorizations.store');
        // 刷新token
        $api->put('authorizations/current','AuthorizationsController@update')->name('api.authorizations.update');
        // 删除token
        $api->delete('authorizations/current','AuthorizationsController@destroy')->name('api.authorizations.destroy');


        // 需要token
        $api->group(['middleware' => 'api.auth'],function ($api){
            // 当前登录用户的用户信息
            $api->get('user', 'UsersController@me')->name('api.user.show');
            // 编辑用户信息
            $api->patch('user','UsersController@update')->name('api.user.update');
            // 图片资源
            $api->post('images','ImagesController@store')->name('api.images.store');
        });
    });
});
