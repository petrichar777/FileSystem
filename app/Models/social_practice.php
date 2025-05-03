<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class social_practice extends Model
{
    use HasFactory;
    protected $table = 'social_practice';
    protected $fillable = ['student_name','student_id','semester','grade','major','class','practice_name','organization','start_date','end_date','practice_proof_material','status','rejection_reason'];
    public $timestamps = false;
    public function approvePractice($status, $rejectionReason = null){
        if ($status === '已驳回' && empty($rejectionReason)) {
            throw new \Exception('驳回状态必须提供驳回原因');
        }
        $this->status = $status;
        $this->rejection_reason = $rejectionReason?: null;
        return $this->save();
    }
}