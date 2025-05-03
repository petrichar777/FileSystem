<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class organizational_resume extends Model
{
    use HasFactory;
    protected $table = 'organizational_resume';
    protected $fillable = ['student_name','student_id','semester','grade','major','class','organization_name','position','start_date','end_date','organization_proof_material','status','rejection_reason'];
    public $timestamps = false;
    public function approveOrganization($status, $rejectionReason = null){
        if ($status === '已驳回' && empty($rejectionReason)) {
            throw new \Exception('驳回状态必须提供驳回原因');
        }
        $this->status = $status;
        $this->rejection_reason = $rejectionReason?: null;
        return $this->save();
    }
}