<?php

namespace App\http\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalDetail extends Model
{
    /**
     * @desc 判断用户是否具备审批权限
     * @param $case_id
     * @return bool
     */
    public static function isHaveApprovalAuth($case_id){
        if(!$case_id){
            return false;
        }
        $caseDetailData = ApprovalDetail::where('approval_id',$case_id)->orderBy('created_at','desc')->first();
        //没有数据或者用户不正确
        if(!$caseDetailData || $caseDetailData['process_userid'] != session('uid')){
            return false;
        }
        return true;
    }

}
