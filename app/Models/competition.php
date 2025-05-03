<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class competition extends Model
{
    use HasFactory;
    protected $table = 'competitions';
    protected $fillable = ['student_name','student_id','semester','grade','major','class','competitions_name','registration_date','competition_level',
        'competition_proof_material','status','rejection_reason','award_level'];
    public $timestamps = false;
    public function approveCompetition($status, $rejectionReason = null){
        if ($status === '已驳回' && empty($rejectionReason)) {
            throw new \Exception('驳回状态必须提供驳回原因');
        }
        $this->status = $status;
        $this->rejection_reason = $rejectionReason?: null;  // 驳回原因为空时设置为 null
        return $this->save();  // 保存更改
    }
}
