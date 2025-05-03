<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class research extends Model
{
    use HasFactory;
    protected $table = 'research';
    protected $fillable = ['student_name','student_id','semester','type','grade','major','class','project_name','project_level','ranking_total','research_proof_material','status','rejection_reason'];
    public $timestamps = false;
    public function approveResearch($status, $rejectionReason = null){
        if ($status === '已驳回' && empty($rejectionReason)) {
            throw new \Exception('驳回状态必须提供驳回原因');
        }
        $this->status = $status;
        $this->rejection_reason = $rejectionReason?: null;  // 驳回原因为空时设置为 null
        return $this->save();  // 保存更改
    }
}
