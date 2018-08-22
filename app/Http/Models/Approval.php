<?php

namespace App\http\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    /**
     * @desc //待处理1 驳回2 拒绝3 同意4
     * @param $status
     */
    public static function getCaseStatus($status){
        switch ($status){
            case 1:
                $return = '待处理';
                break;
            case 2:
                $return = '已驳回';
                break;
            case 3:
                $return = '已拒绝';
                break;
            case 4:
                $return = '已同意';
                break;
            default :
                return false;
        }
        return $return;
    }

    /**
     * @desc 判断当前用户是否能够进行撤回修改操作 待处理和被驳回都具备权限
     * @param $case_id
     * @return bool
     */
    public static function isHaveAuth($approval_id){
        if(!$approval_id){
            return false;
        }
        $caseData = Approval::where('id',$approval_id)->first();

        //没有数据或者用户不正确
        if(!$caseData || $caseData['user_id'] != session('uid')){
            return false;
        }

        //判断是否进入下一流程
        $caseDetailData = ApprovalDetail::where('approval_id',$approval_id)->orderBy('id','desc')->first();
        //发起后待处理和被驳回都具备权限
        if(($caseDetailData['process_id'] === 0 && $caseDetailData['approval_detail_status'] == 1) || $caseDetailData['approval_detail_status'] == 2){
            return true;
        }
        return false;
    }

    //获取并组合所有的审批数据
    public static function getMyApproval($approvalArrRes){
        foreach ($approvalArrRes as $k => $v){

            $flowData = Workflow::find($v['flow_id']);
            $caseDetailData = ApprovalDetail::where("approval_id",$v['id'])->orderBy("created_at","desc")->first();

            $approvalArrRes[$k]['flow_name']            = $flowData['flow_name'];
            $approvalArrRes[$k]['process_user_name']    = $caseDetailData['process_user_name'];
            $approvalArrRes[$k]['process_name']         = $caseDetailData['process_name'];
            $approvalArrRes[$k]['approval_detail_comment']   = $caseDetailData['case_detail_comment'];
            //待处理1 驳回2 拒绝3 完结4
            $approvalArrRes[$k]['approval_detail_status']   = Approval::getCaseStatus($caseDetailData['approval_detail_status']);
            $approvalArrRes[$k]['flow_status']   = $flowData['flow_status'];
            //判断是否具有修改/撤回权限(有没有进入下个流程)
            $approvalArrRes[$k]['is_auth'] = ($flowData['flow_status'] == 2) ? false : Approval::isHaveAuth($v['id']);
        }
        return $approvalArrRes;
    }


}
