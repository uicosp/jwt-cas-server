<?php
/**
 * @author: Yudu <uicosp@gmail.com>
 * @date: 2016/12/24
 */

namespace Uicosp\JwtCasServer;

use App\Http\Controllers\Controller;
use App\User;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class WechatController extends Controller
{
    use JwtTrait;

    protected $wechat;
    protected $openIdField;

    /**
     * JwtController constructor.
     * @param Request $request
     * @param Application $wechat
     */
    public function __construct(Request $request, Application $wechat)
    {
        $this->wechat = $wechat;
    }

    public function login(Request $request)
    {
        // 先尝试静默授权，如果用户不存在则调用显示授权
        return $this->wechat->oauth->scopes(['snsapi_base'])->redirect(route('wechatBaseCallback') . '?' . $this->passQueries($request));
    }

    /**
     * 静默回调（scope=snsapi_base）
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function baseCallback(Request $request)
    {
        $wechatUser = $this->wechat->oauth->user();

        // 用户不存在则需要用户进行授权
        if (!$user = User::where(['openid' => $wechatUser->getId()])->first()) {
            return $this->wechat->oauth->scopes(['snsapi_userinfo'])->redirect(route('wechatUserinfoCallback') . '?' . $this->passQueries($request));
        }

        return $this->createTokenAndRedirectWrapper($request, $user);
    }

    /**
     * 登陆回调（scope=snsapi_userinfo）
     * @param Request $request
     * @return mixed
     */
    public function userinfoCallback(Request $request)
    {
        // 从微信取得用户详细信息并创建用户
        $wechatUser = $this->wechat->oauth->user();
        $user = User::create(['name' => $wechatUser->getName(), 'password' => bcrypt(str_random(10)),
            'openid' => $wechatUser->getId(),
            'unionid' => $wechatUser->getOriginal()->unionid,
        ]);

        return $this->createTokenAndRedirectWrapper($request, $user);
    }

    /**
     * 传递请求中的 cas_redirect_url 参数
     * @param Request $request
     * @return string
     */
    protected function passQueries(Request $request)
    {
        return 'cas_redirect_url=' . $request->input('cas_redirect_url');
    }

    protected function createTokenAndRedirectWrapper($request, $user)
    {
        return $this->createTokenAndRedirect($request, app(JWTAuth::class), $user, [
            'openid' => $user->openid,
        ]);
    }
}
