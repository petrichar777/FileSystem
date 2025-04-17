<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Auth\User as Authenticatable;

class members extends Authenticatable implements JWTSubject
{
    protected $table = 'members';
    use HasFactory;
    protected $fillable = [
        'real_name', 'phone', 'password', 'email', 'gender',
        'school', 'major', 'degree', 'organization',
        'position', 'technical_title', 'research_field'
    ];
    protected $primaryKey = 'member_id';
    public $timestamps = false;
    // JWT 相关方法
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    //会员注册
    public static function register(array $data)
    {
        $member = new self();
        $member->fill($data);
        $member->password = Hash::make($data['password']);
        $member->save();
        return $member;
    }
    //会员使用密码登录
    public static function passwordLogin($phone, $password)
    {
        $member = members::where('phone', $phone)->first();
        if ($member == null) {
            return '用户不存在';
        }
        // 验证密码
        if (!Hash::check($password, $member->password)) {
           return '密码错误';
        }
        // 生成token
        $token = JWTAuth::fromUser($member);
//        // 缓存token
//        Cache::put('token_' . $member->member_id, $token, 60 * 2); // 缓存24小时
        return $token;
    }
}
