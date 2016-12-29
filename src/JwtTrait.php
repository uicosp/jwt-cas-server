<?php
/**
 * @author: Yudu <uicosp@gmail.com>
 * @date: 2016/12/26
 */

namespace Uicosp\JwtCasServer;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

Trait JwtTrait
{
    /**
     * 根据已有的用户实例来生成 jwt_token 并重定向（用于第三方登陆，如微信）
     * @param Request $request
     * @param JWTAuth $jwt
     * @param $user
     * @param array $customClaims
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function createTokenAndRedirect(Request $request, JWTAuth $jwt, $user, array $customClaims = [])
    {
        try {
            $token = $jwt->fromUser($user, $customClaims);
        } catch (JWTException $e) {
            return abort(500, 'Token创建失败');
        }

        return $this->casRedirect($request, $token);
    }

    /**
     * 重定向
     * @param Request $request
     * @param $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function casRedirect(Request $request, $token)
    {
        $redirectUrl = urldecode($request->input('cas_redirect_url'));
        // 为确保 jwt_token 的安全性必须使用 https 连接
        return redirect($redirectUrl . "?jwt_token={$token}");
    }
}