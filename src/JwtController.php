<?php
/**
 * @author: Yudu <uicosp@gmail.com>
 * @date: 2016/12/24
 */

namespace Uicosp\JwtCasServer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\JWTAuth;

class JwtController extends Controller
{
    use JwtTrait;

    protected $jwt;

    /**
     * JwtController constructor.
     * @param JWTAuth $jwt
     */
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function showLoginForm(Request $request)
    {
        return view('JwtCasServer::login', $request->only('cas_redirect_url'));
    }

    public function login(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        try {
            if (!$token = $this->jwt->attempt($credentials)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'password' => '用户名或密码错误'
                    ]);
            }
        } catch (JWTException $e) {
            return abort(500, 'Token创建失败');
        }

        return $this->casRedirect($request, $token);
    }

    protected function username()
    {
        return 'email';
    }

    public function authenticate()
    {
        try {
            if (!$payload = $this->jwt->parseToken()->getPayload()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json($payload->get());
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = $this->jwt->parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }
}
