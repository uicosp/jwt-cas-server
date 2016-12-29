<?php
Route::group(['prefix' => 'jwt', 'namespace' => 'Uicosp\JwtCasServer'], function () {
    /*------------------------------登陆------------------------------*/
    // 原生用户名密码登陆
    Route::group(['middleware' => 'web'], function () {
        Route::get('login', 'JwtController@showLoginForm');
        Route::post('login', 'JwtController@login');
    });

    // 微信登陆
    Route::get('login-via-wechat', 'WechatController@login');
    Route::group(['prefix' => 'callback/wechat'], function () {
        Route::get('base', 'WechatController@baseCallback')->name('wechatBaseCallback');
        Route::get('userinfo', 'WechatController@userinfoCallback')->name('wechatUserinfoCallback');
    });

    /*------------------------------API------------------------------*/
    Route::group(['middleware' => 'api'], function () {
        Route::post('auth', 'JwtController@authenticate');
        Route::post('user', 'JwtController@getAuthenticatedUser');
    });

    Route::any('test/{a}','WechatController@getSecret');
});
