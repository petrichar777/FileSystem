<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

class students extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $table = 'students';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'name', 'student_number', 'password', 'email','gender', 'grade', 'major', 'class', 'birth_date', 'political_status', 'volunteer_hours'
    ];
    protected $hidden = [
        'password',
    ];
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
            'student_number' => $this->student_number,
            'class' => $this->class,
        ];
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);  // 使用 bcrypt 加密密码
    }
    public function StuRegister($name,$student_number,$password,$email,$gender,$grade,$major,$class,$birth_date)
    {
        return $this->create([
            'name' => $name,
            'student_number' => $student_number,
            'password' => $password,
            'email' => $email,
            'gender' => $gender,
            'grade' => $grade,
            'major' => $major,
            'class' => $class,
            'birth_date' => $birth_date,
        ]);
    }
}
