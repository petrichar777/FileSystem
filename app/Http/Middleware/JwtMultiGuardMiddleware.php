<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class JwtMultiGuardMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        try {
            // 设置 guard
            Auth::shouldUse($guard);

            // 验证 token 并尝试认证用户
            $user = JWTAuth::setRequest($request)->parseToken()->authenticate();

            if (!$user) {
                return json_fail('用户未登录', null, 100);
            }
        } catch (JWTException $e) {
            return response()->json(['msg' => 'Token 错误或已过期'], 401);
        }

        return $next($request);
    }

}
