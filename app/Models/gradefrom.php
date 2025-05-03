<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gradefrom extends Model
{
    use HasFactory;
    protected $table = 'gradefrom';
    protected $fillable = ['student_name','student_id','semester','grade','major','class','course_name','course_score','grade_proof_material','status','rejection_reason'];
    public $timestamps = false;
    public function approveGrade($status, $rejectionReason = null){
        if ($status === '已驳回' && empty($rejectionReason)) {
            throw new \Exception('驳回状态必须提供驳回原因');
        }
        $this->status = $status;
        $this->rejection_reason = $rejectionReason?: null;
        return $this->save();
    }
}