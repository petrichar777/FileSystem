<?php
namespace App\Helpers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class tokenHelper
{
    /**
     * 获取当前登录用户的 Token Payload
     * @return \Tymon\JWTAuth\Payload|null
     */
    public static function getTokenPayload()
    {
        try {
            return JWTAuth::parseToken()->getPayload();
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * 获取 Token 中的某个字段
     * @param string $key
     * @return mixed|null
     */
    public static function getFromToken(string $key)
    {
        $payload = self::getTokenPayload();
        return $payload ? $payload->get($key) : null;
    }
}
