<?php


use App\Http\Controllers\WwjController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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
//学生注册
Route::post('students/register',[WwjController::class,'StuRegister']);
//学生登录
Route::post('/students/login', [WwjController::class, 'StuLogin']);
//教师注册
Route::post('teachers/register',[WwjController::class,'TeaRegister']);
//教师登录
Route::post('/teachers/login', [WwjController::class, 'TeaLogin']);

//学生中间件
Route::middleware(['jwt.guard:students'])->group(function () {
    //学生退出登录
    Route::post('/students/logout', [WwjController::class, 'logout']);
    //学生文件上传
    Route::post('/students/upload', [WwjController::class, 'upload']);
});

Route::middleware(['jwt.guard:teachers'])->group(function () {
    //查看教师信息
    Route::get('/teachers/profile', [WwjController::class, 'profile']);
    //教师退出登录
    Route::post('/teachers/logout', [WwjController::class, 'logout']);
    //获取所有教师信息
    Route::get('/getAllTeachers', [WwjController::class, 'getAllTeachers']);
    //修改教师身份
    Route::post('/updateTeacher', [WwjController::class, 'updateTeacher']);
    //查询学生信息
    Route::get('/getAllStudents', [WwjController::class, 'getAllStudents']);
    //搜索学生信息
    Route::get('/searchStudent', [WwjController::class, 'searchStudent']);
    //教师审批创新创业
    Route::post('/approveInnovation', [WwjController::class, 'approveInnovation']);
    //教师审批科研
    Route::post('/approveResearch', [WwjController::class, 'approveResearch']);
    //教师审批竞赛
    Route::post('/approveCompetition', [WwjController::class, 'approveCompetition']);
});









