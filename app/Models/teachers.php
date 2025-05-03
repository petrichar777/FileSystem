<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class teachers extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $table = 'teachers';
    protected $fillable = ['name','account','password','role','department'];
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 返回一个包含自定义声明的关联数组。
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,  // 假设你有一个 'id' 字段用于标识用户
            'name' => $this->name,
            'account' => $this->account,
            'role' => $this->role,
            'department' => $this->department,
        ];
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);  // 使用 bcrypt 加密密码
    }

    // 教师注册
    public function TeaRegister($name,$account, $password, $role, $department){
        return $this->create([
            'name' => $name,
            'account' => $account,
            'password' => $password,
            'role' => $role,
            'department' => $department,
        ]);
    }
}
