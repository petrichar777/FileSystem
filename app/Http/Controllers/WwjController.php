<?php

namespace App\Http\Controllers;

use App\Models\competition;
use App\Models\innovation;
use App\Models\research;
use App\Models\students;
use App\Models\teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class WwjController extends Controller
{
    public function StuRegister(Request $request)
    {
        //学生注册
        $validated = $request->validate ([
            'name' => 'required|string|max:255',
            'student_number' =>'required|string|max:255|unique:students',
            'password' =>'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'gender' =>'required|string|max:255',
            'grade' =>'required|string|max:255',
            'major' =>'required|string|max:255',
            'class' =>'required|string|max:255',
            'birth_date' =>'required|date',
        ]);
        if (!$validated) {
            return json_fail('注册失败, 数据验证失败', null, 100);
        }
        $email = $validated['email'];
        $register = (new \App\Models\students)->Sturegister(
            $validated['name'],
            $validated['student_number'],
            $validated['password'],
            $validated['email'],
            $validated['gender'],
            $validated['grade'],
            $validated['major'],
            $validated['class'],
            $validated['birth_date'],

        );

        if (!$register) {
            return json_fail('注册失败, 用户数据为空', null, 101);
        }

        return json_success('注册成功', $register, 200);
    }

    //学生登录
    public function StuLogin(Request $request){
        $validated = $request->validate([
            'student_number' =>'required|string|max:255',
            'password' =>'required|string|max:255',
        ]);
        if (!$validated) {
            return json_fail('登录失败, 数据验证失败', null, 100);
        }
        $student_number = $validated['student_number'];
        $password = $validated['password'];
        $student = students::where('student_number', $student_number)->first();
        if (!$student) {
            return json_fail('登录失败, 该用户不存在', null, 101);
        }
        if (!Hash::check($password, $student->password)) {
            return json_fail('登录失败, 密码错误', null, 102);
        }
        $credentials = [
            'student_number' => $validated['student_number'],
            'password' => $validated['password'],
        ];
        $token = Auth::guard('students')->attempt($credentials);
        if (!$token) {
            return json_fail('登录失败, 生成 Token 失败', null, 103);
        }
        return json_success('学生登录成功', [
            'id' => $student->id,
            'name' => $student->name,
            'student_number' => $student->student_number,
            'gender' => $student->gender,
            'grade' => $student->grade,
            'major' => $student->major,
            'class' => $student->class,
            'token' => $token,
        ], 200, 200);
    }

    //教师注册
    public function TeaRegister(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account' =>'required|string|max:255|unique:teachers',
            'password' =>'required|string|max:255',
            'role' =>'required|string|max:255',
            'department' =>'required|string|max:255',
        ]);
        if (!$validated) {
            return json_fail('注册失败, 数据验证失败', null, 100);
        }
        $name = $validated['name'];
        $account = $validated['account'];
        $password = $validated['password'];
        $role = $validated['role'];
        $department = $validated['department'];
        $register = (new \App\Models\teachers)->TeaRegister(
            $validated['name'],
            $validated['account'],
            $validated['password'],
            $validated['role'],
            $validated['department'],
        );
        if (!$register) {
            return json_fail('注册失败, 用户数据为空', null, 101);
        }
        return json_success('注册成功', $register, 200);
    }
    //教师登录
    public function TeaLogin(Request $request){
        $validated = $request->validate([
            'account' =>'required|string|max:255',
            'password' =>'required|string|max:255',
        ]);
        if (!$validated) {
            return json_fail('登录失败, 数据验证失败', null, 100);
        }
        $account = $validated['account'];
        $password = $validated['password'];
        $teacher = teachers::where('account', $account)->first();
        if (!$teacher) {
            return json_fail('登录失败, 该用户不存在', null, 101);
        }
        if (!Hash::check($password, $teacher->password)) {
            return json_fail('登录失败, 密码错误', null, 102);
        }
        $token = Auth::guard('teachers')->attempt($validated);
        return json_success('教师登录成功', [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'account' => $teacher->account,
            'role' => $teacher->role,
            'department' => $teacher->department,
            'token' => $token,
        ], 200, 200);
    }

    //获取当前登录的教师信息
    public function profile(Request $request){
        try{
            $teacher = Auth::user();
            if(!$teacher){
                return json_fail('获取失败, 未登录', null, 100);
            }
            return json_success('获取成功', [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'account' => $teacher->account,
                'department' => $teacher->department,
                'role' => $teacher->role,
            ], 200);
        }catch (\Exception $e){
            return json_fail('token无效或已过期', null, 100);
        }
    }

    //OSS文件上传接口
    public function upload(Request $request){
        $request->validate([
            'file' => 'required|file',
            'student_id'=>'required|integer',
            'type'=>'required|string',
        ]);
        $file = $request->file('file');
        $studentId = $request->input('student_id');
        $type = $request->input('type');
        $date = date('Y-m-d');
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        //拼接OSS路径
        $ossFolder = "student_files/{$type}/{$studentId}/{$date}";
        $fullPath = $ossFolder . '/' . $filename;
        //上传到OSS
        Storage::disk('oss')->put($fullPath, file_get_contents($file));
        //获取文件URL
        $url = Storage::disk('oss')->url($fullPath);
        return json_success('上传成功', [
            'url' => $url,
            'path' => $fullPath,
            'student_id' => $studentId,
            'type' => $type,
        ],200,200);
    }

    //获取所有教师信息接口
    public function getAllTeachers(Request $request){
        $request->validate([
            'size' =>'required|integer|min:10|max:100',
            'page' =>'required|integer|min:1',
        ]);
            $size = $request->input('size');
            $page = $request->input('page');
            $data = Auth::user();
            if (!$data) {
                return json_fail('获取失败, 未登录', null, 100);
            }
            $role = $data->role;
            if($data -> role != '管理员'){
                return json_fail('权限不足', null, 101);
            }
            $teachersQuery = teachers::query();
            $teachers = $teachersQuery->paginate($size, ['*'], 'page', $page);
            return json_success('获取成功',[
                'data' => $teachers->items(),
                'current_page' => $teachers->currentPage(),
                'per_page' => $teachers->perPage(),
                'total' => $teachers->total(),
            ],200,200);
        }

    //修改教师身份
    public function updateTeacher(Request $request){
        try {
            $data = Auth::user();
            $request->validate([
                'teacher_id' =>'required|integer',
                'role' =>'required|string',
            ]);
            if (!$data) {
                return json_fail('获取失败, 未登录', null, 100);
            }
            if($data -> role!= '管理员'){
                return json_fail('权限不足', null, 101);
            }
            $TeacherId = $request->input('teacher_id');
            //获取待修改的教师信息
            $teacher = teachers::find($TeacherId);
            if(!$teacher){
                return json_fail('获取失败, 该教师不存在', null, 102);
            }
            $teacher -> role = $request->input('role');
            $teacher -> save();
            return json_success('修改成功', $teacher, 200, 200);
        }catch (\Exception $e){
            return json_fail('token无效或已过期', null, 100);
        }
    }

    //查看所有学生信息
    public function getAllStudents(Request $request)
    {
        $request->validate([
            'size' => 'required|integer|min:10|max:100',
            'page' => 'required|integer|min:1',
        ]);

        $size = $request->input('size');
        $page = $request->input('page');

        // 获取当前登录用户信息
        $data = Auth::user();
        if (!$data) {
            return json_fail('获取失败, 未登录', null, 100);
        }

        $department = $data->department;
        $role = $data->role;
        // 权限验证
        if ($role != '管理员' && $role != '系主任') {
            return json_fail('权限不足', null, 101);
        }
        // 管理员获取所有学生
        if ($role == '管理员') {
            $studentsQuery = students::query();
        } else {
            // 系主任获取同专业的学生
            $studentsQuery = students::where('major', $department);
        }
        // 分页查询
        $students = $studentsQuery->paginate($size, ['*'], 'page', $page);
        // 返回结果
        return json_success('获取成功', [
            'data' => $students->items(),
            'current_page' => $students->currentPage(),
            'per_page' => $students->perPage(),
            'total' => $students->total(),
        ], 200, 200);
    }

    //搜索查询学生信息接口（按学生姓名模糊查询）
    public function searchStudent(Request $request){
        $request->validate([
            'name' =>'required|string',
            'size' =>'required|integer|min:10|max:100',
            'page' =>'required|integer|min:1',
        ]);
        try {
            $size = $request->input('size');
            $page = $request->input('page');
            $data = Auth::user();
            if (!$data) {
                return json_fail('获取失败, 未登录', null, 100);
            }
            $department = $data->department;
            $role = $data->role;
            if($role!= '管理员' && $role!= '系主任'){
                return json_fail('权限不足', null, 101);
            }
            $name = $request->input('name');
            if($role == '管理员'){
                $studentsQuery = students::where('name', 'like', '%'. $name. '%');
                $students = $studentsQuery->paginate($size, ['*'], 'page', $page);
            }else{
                $studentsQuery = students::where('major', $department)->where('name', 'like', '%'. $name. '%');
                $students = $studentsQuery->paginate($size, ['*'], 'page', $page);
            }
            return json_success('获取成功',[
                'data' => $students->items(),
                'current_page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ]);
        }catch (\Exception $e){
            return json_fail('token无效或已过期', null, 100);
        }
    }
    //教师审批创新创业
    public function approveInnovation(Request $request){
        $request->validate([
            'innovation_id' => 'required|integer|exists:innovation,id',  // 确保存在该创新创业记录
            'status' => 'required|string|in:已通过,已驳回',  // 状态只能是 "已通过" 或 "已驳回"
            'rejection_reason' => 'nullable|string',  // 驳回原因可选
        ]);
        $innovationId = $request->input('innovation_id');
        $status = $request->input('status');
        $rejectionReason = $request->input('rejection_reason');
        $data = Auth::user();
        if (!$data) {
            return json_fail('获取失败, 未登录', null, 100);
        }
        $role = $data->role;
        $innovation = Innovation::find($request->innovation_id);
        if (!$innovation) {
            return json_fail('创新创业记录不存在', null, 404);
        }
    // 调用实例方法
        $innovation->approveInnovation($status,$rejectionReason);
        return json_success('审批成功', $innovation, 200, 200);
    }

    //教师审批科研
    public function approveResearch(Request $request){
        $request->validate([
            'research_id' =>'required|integer|exists:research,id',  // 确保存在该科研记录
           'status' =>'required|string|in:已通过,已驳回',  // 状态只能是 "已通过" 或 "已驳回"
          'rejection_reason' => 'nullable|string',  // 驳回原因可选
        ]);
        $researchId = $request->input('research_id');
        $status = $request->input('status');
        $rejectionReason = $request->input('rejection_reason');
        $data = Auth::user();
        if (!$data) {
            return json_fail('获取失败, 未登录', null, 100);
        }
        $role = $data->role;
        $research = research::find($request->research_id);
        if (!$research) {
            return json_fail('科研记录不存在', null, 404);
        }
        $research->approveResearch($status,$rejectionReason);
        return json_success('审批成功', $research, 200, 200);
    }

    //教师审批竞赛
    public function approveCompetition(Request $request){
        $request->validate([
           'competition_id' =>'required|integer|exists:competitions,id',  // 确保存在该科研记录
          'status' =>'required|string|in:已通过,已驳回',  // 状态只能是 "已通过" 或 "已驳回"
        'rejection_reason' => 'nullable|string',  // 驳回原因可选
        ]);
        $competitionId = $request->input('competition_id');
        $status = $request->input('status');
        $rejectionReason = $request->input('rejection_reason');
        $data = Auth::user();
        if (!$data) {
            return json_fail('获取失败, 未登录', null, 100);
        }
        $role = $data->role;
        $competition = competition::find($request->competition_id);
        if (!$competition) {
            return json_fail('竞赛记录不存在', null, 404);
        }
        $competition->approveCompetition($status,$rejectionReason);
        return json_success('审批成功', $competition, 200, 200);
    }

    //退出登录
    public function logout(Request $request){
        $token = $request->header('Authorization');
        if (!$token) {
            return json_fail('退出失败, 未提供 Token', null, 100);
        }
        JWTAuth::invalidate($token);
        return json_success('退出成功', null, 200);
    }

}
