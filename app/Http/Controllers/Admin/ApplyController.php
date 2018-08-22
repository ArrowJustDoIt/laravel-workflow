<?php

namespace App\Http\Controllers\admin;

use App\http\Models\Approval;
use App\http\Models\ApprovalDetail;
use App\Http\Models\Workflow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApplyController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $map = [
            'approval_details.approval_detail_status'       => 1,
            'approval_details.process_userid'               => session('uid'),
        ];
        if($flow_id = intval($request->input('flow_id'))){
            $map['approval_details.flow_id'] = $flow_id;
            $returnData['flow_id'] = $flow_id;
        }
        $toDoList = ApprovalDetail::join('approvals','approval_details.approval_id','=','approvals.id')
            ->where($map)->paginate(15);
        $toDoListArr = $toDoList->toArray();

        $flowNameData = Workflow::getFlowName();
        foreach ($toDoListArr['data'] as $k => $v){
            $toDoListArr['data'][$k]['flow_name'] = $flowNameData[$v['flow_id']];
            $toDoListArr['data'][$k]['u_cn_name'] = self::$userArr[$v['user_id']];
            $flowStatus = Workflow::getWorkflowStatus($v['flow_id']);
            if($flowStatus == 2){
                //已禁用
                unset($toDoListArr['data'][$k]);
            }
        }
        $returnData['toDoList'] = $toDoList;
        $returnData['toDoListArr'] = $toDoListArr['data'];
        $returnData['flowNameData'] = $flowNameData;

        return view('apply.index',$returnData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function judge(Request $request)
    {
        $data = $request->only(['id','type','value']);
        $case_id            = $data['id'];
        $case_detail_status = $data['type'];
        $case_detail_common = $data['value'];
        if(!$case_id || !$case_detail_status){
            return response()->json(['code' => 500,'msg' => '数据错误']);
        }
        //二重判断用户是否有修改权限
        if(!ApprovalDetail::isHaveApprovalAuth($case_id)){
            return response()->json(['code' => 500,'msg' => '不具备权限']);
        }

        //入库下一节点新数据
        $caseData       = Approval::find($case_id);
        $caseDetailData = ApprovalDetail::where("approval_id",$case_id)->orderBy("id","desc")->first();
        $flowData       = Workflow::find($caseData['flow_id']);
        $flowData       = json_decode($flowData['flow_process'],true);
        //当前步骤
        $newProcessId   = intval($caseDetailData['process_id']) + 1;
        //casedetail表更新
        $updateCaseDetailData = [
            'approval_detail_status' => $case_detail_status,
            'approval_detail_comment' => $case_detail_common,
            'updated_at'             => date("Y-m-d H:i:s",time()),
        ];
        //更新数据为已完结
        $updateCaseData = [
            'status' => 2,
        ];

        //如果 节点+1 超过预设节点数,则说明申请流程走完
        if($newProcessId > (count($flowData) - 1)){
            if($case_detail_status == 2){
                //驳回更新字段,不入库新节点
                $caseDetailRes  = ApprovalDetail::where("id",$caseDetailData['id'])->update($updateCaseDetailData);
                if($caseDetailRes){
                    return response()->json(['code' => 200,'msg' => '审批成功']);
                }else{
                    return response()->json(['code' => 500,'msg' => '审批失败,请重试']);
                }
            }else{
                //拒绝则更新case表数据为已完结
                DB::beginTransaction();
                $caseDetailRes = ApprovalDetail::where("id",$caseDetailData['id'])->update($updateCaseDetailData);
                $caseRes = Approval::where("id",$case_id)->update($updateCaseData);
                if($caseDetailRes && $caseRes){
                    DB::commit();
                    return response()->json(['code' => 200,'msg' => '审批成功']);
                }else{
                    DB::rollback();
                    return response()->json(['code' => 500,'msg' => '审批失败,请重试']);
                }
            }
        }else{
            if($case_detail_status == 2){
                //驳回更新字段,不入库新节点
                $caseDetailRes  = ApprovalDetail::where("id",$caseDetailData['id'])->update($updateCaseDetailData);
                if($caseDetailRes){
                    return response()->json(['code' => 200,'msg' => '审批成功']);
                }else{
                    return response()->json(['code' => 500,'msg' => '审批失败,请重试']);
                }
            }elseif($case_detail_status == 3){
                //拒绝则更新case表数据为已完结
                DB::beginTransaction();
                $caseDetailRes = ApprovalDetail::where("id",$caseDetailData['id'])->update($updateCaseDetailData);
                $caseRes = Approval::where("id",$case_id)->update($updateCaseData);
                if($caseDetailRes && $caseRes){
                    DB::commit();
                    return response()->json(['code' => 200,'msg' => '审批成功']);
                }else{
                    DB::rollback();
                    return response()->json(['code' => 500,'msg' => '审批失败,请重试']);
                }
            }elseif($case_detail_status == 4){
                //如果是同意 入库新节点数据
                $newInsertData = [
                    'flow_id'               => $caseData['flow_id'],
                    'process_id'            => $newProcessId,
                    'process_userid'        => $flowData[$newProcessId]['process_user'],
                    'process_user_name'     => $flowData[$newProcessId]['process_user_name'],
                    'process_name'          => $flowData[$newProcessId]['process_name'],
                    'approval_id'           => $case_id,
                    'created_at'            => date('Y-m-d H:i:s',time()),
                    'updated_at'            => date('Y-m-d H:i:s',time()),
                ];
                //更新现有字段
                DB::beginTransaction();
                $caseDetailRes  = ApprovalDetail::where("id",$caseDetailData['id'])->update($updateCaseDetailData);
                $caseRes        = ApprovalDetail::insert($newInsertData);
                if($caseDetailRes && $caseRes){
                    DB::commit();
                    return response()->json(['code' => 200,'msg' => '审批成功']);
                }else{
                    DB::rollback();
                    return response()->json(['code' => 500,'msg' => '审批失败,请重试']);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
