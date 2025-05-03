<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class innovation extends Model
{
    use HasFactory;
    protected $table = 'innovation';
    protected $fillable = ['student_id', 'semester','grade','major','class','company_name','is_virtual',
        'rank','registration_date','company_size','innovation_proof_material','status','rejection_reason'];
    public $timestamps = false;

    //教师审批创新创业
    public function approveInnovation($status, $rejectionReason = null)
    {
        // 1. 确保 $this 是模型实例
        if (!$this instanceof Innovation) {
            throw new \Exception('Invalid model instance');
        }

        // 2. 判断审批状态为 "已驳回" 时，是否提供了驳回原因
        if ($status === '已驳回' && empty($rejectionReason)) {
            throw new \Exception('驳回状态必须提供驳回原因');
        }

        // 3. 更新审批状态和驳回原因（如果有）
        $this->status = $status;
        $this->rejection_reason = $rejectionReason ?: null;  // 驳回原因为空时设置为 null
        return $this->save();  // 保存更改
    }
}
