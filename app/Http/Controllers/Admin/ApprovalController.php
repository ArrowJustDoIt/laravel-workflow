<?php

namespace App\Http\Controllers\admin;

use App\http\Models\Approval;
use App\http\Models\ApprovalDetail;
use App\Http\Models\Workflow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $map = [
            'flow_status' => 1,
        ];
        $keyword = $request->input('keyword');
        if ($keyword) {
            $map[] = array('flow_name','like',"%{$keyword}%");
        }

        $workflowRes = Workflow::where($map)->paginate(1);
        return view('approval.index',['workflowRes'=>$workflowRes,'keyword'=>$keyword]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $flow_id = $request->input("id");
        if(!$flow_id){
            exit;
        }
        $flowData = Workflow::find($flow_id);
        $flowData = json_decode($flowData['flow_design'],true);
        //获取表单
        $flowHtmlData = \App\Http\Models\Workflow::createTableHtmlByData($flowData['fields']);
        return view('approval.create',['html'=>$flowHtmlData,'id'=>$flow_id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');
            $data = $request->input();
            $data['case_userid'] = session('uid');
            Validator::make($data, [
                'id'            =>  'required',
                'case_title'    =>  'required',
                'case_desc'     =>  'required',
                'case_userid'   =>  'required',
            ], [
                'required' => ':attribute 为必填项',
            ], [
                'id' => '名称',
                'case_title'    => '申请标题',
                'case_desc'     => '申请描述',
                'case_userid'   => '用户登录',
            ])->validate();


            //入库
            $saveData = [
                'flow_id'       => $id,
                'user_id'       => $data['case_userid'],
                'title'         => $data['case_title'],
                'desc'          => $data['case_desc'],
                'design'        => json_encode($data),
                'created_at'    => date('Y-m-d H:i:s',time()),
                'updated_at'    => date('Y-m-d H:i:s',time()),

            ];
            DB::beginTransaction();
            $insertId = Approval::insertGetId($saveData);

            //获取process_userid和process_user_name
            $flowData = Workflow::find($id);
            $processData = json_decode($flowData['flow_process'],true);
            $caseData = [
                'flow_id'           => $id,
                'approval_id'       => $insertId,
                'process_userid'    => $processData[0]['process_user'],
                'process_name'      => $processData[0]['process_name'],
                'process_user_name' => $processData[0]['process_user_name'],
                'created_at'        => date('Y-m-d H:i:s',time()),
                'updated_at'        => date('Y-m-d H:i:s',time()),
            ];
            $insertData = ApprovalDetail::insert($caseData);
            if($insertId && $insertData){
                Db::commit();
                return redirect(route('approvals.lists'))->with('success','入库成功');
            }else{
                Db::rollback();
                return back()->with('error','入库失败,请重试');
            }
        }
    }

    //获取所有我的审批
    public function lists(){
        $uid = session('uid');
        $approvalRes = Approval::where('user_id',$uid)->orderBy('created_at','desc')->paginate(15);
        $approvalArrRes = $approvalRes->toArray();
        $returnData['approvalRes'] = $approvalRes;
        $returnData['myApprovalList'] = [];
        if(!empty($approvalArrRes['data'])){
            $myApprovalList = Approval::getMyApproval($approvalArrRes['data']);
            $returnData['myApprovalList'] = $myApprovalList;
        }
        return view('approval.lists',$returnData);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //获取表单
        $caseData = Approval::find($id)->toArray();
        $flowData = Workflow::find($caseData['flow_id']);
        if($flowData['flow_design']){
            $caseDataArr = json_decode($caseData['design'],true);
            $flowDataArr = json_decode($flowData['flow_design'],true);
            $flowHtmlData = \App\Http\Models\Workflow::createTableHtmlByFieldData($flowDataArr['fields'],$caseDataArr);
        }
        $processData = ApprovalDetail::where("approval_id",$id)->orderBy("id","asc")->get();
        //获取流程节点
        if($processData){
            foreach ($processData as $k => $v){
                $processData[$k]['status'] = Approval::getCaseStatus($v['approval_detail_status']);
            }
        }
        return view('approval.show',['html'=>$flowHtmlData,'ApprovalData'=>$caseData,'flowData'=>$flowData,'processData'=>$processData]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //判断是否进入下一流程,能否撤回
        if(!Approval::isHaveAuth($id)){
            return redirect(route('approvals.index'))->with('error','已经进入流程,不能修改!');
        }
        //获取表单
        $caseData = Approval::find($id)->toArray();
        $flowData = Workflow::find($caseData['flow_id']);
        if($flowData['flow_design']){
            $caseDataArr = json_decode($caseData['design'],true);
            $flowDataArr = json_decode($flowData['flow_design'],true);
            $flowHtmlData = \App\Http\Models\Workflow::createTableHtmlByFieldData($flowDataArr['fields'],$caseDataArr);
        }
        return view('approval.edit',['id'=>$id,'html'=>$flowHtmlData,'ApprovalData'=>$caseData,'flowData'=>$flowData]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->isMethod('put')){
            //判断是否进入下一流程,能否撤回
            if(!Approval::isHaveAuth($id)){
                return redirect(route('approvals.index'))->with('error','已经进入流程,不能修改!');
            }
            //入库
            $saveData = [
                'title'         => $request->input('title'),
                'desc'          => $request->input('desc'),
                'design'        => json_encode($request->input()),
                'updated_at'    => date("Y-m-d H:i:s",time()),
            ];
            $caseDatailData = ApprovalDetail::where("id",$id)->orderBy("id","desc")->first();

            //同时修改最新节点的状态为待处理
            if($caseDatailData['approval_detail_status'] == 1){
                $updateRes = Approval::where("id",$id)->update($saveData);
                if($updateRes){
                    return redirect()->action('Admin\ApprovalController@edit', ['id'=>$id])->with('success','申请成功');
                }else{
                    return redirect()->action('Admin\ApprovalController@edit', ['id'=>$id])->with('error','申请失败,请重试');
                }
            }else{
                DB::beginTransaction();
                $updateCaseData = [
                    'approval_detail_status'    => 1,
                    'updated_at'                => date("Y-m-d H:i:s",time()),
                ];
                $updateCaseDetailRes = ApprovalDetail::where("id",$caseDatailData['id'])->update($updateCaseData);
                $updateRes = Approval::where("id",$id)->update($saveData);

                if($updateRes && $updateCaseDetailRes){
                    DB::commit();
                    return redirect()->action('Admin\ApprovalController@edit', ['id'=>$id])->with('success','申请成功');
                }else{
                    DB::rollback();
                    return redirect()->action('Admin\ApprovalController@edit', ['id'=>$id])->with('error','申请失败,请重试');
                }
            }
        }
    }

    //撤回
    public function rollback(Request $request){
        $id = $request->input("id");
        if(!$id){
            return response()->json(['code' => 500,'msg' => '数据错误']);
        }
        //判断是否进入下一流程,能否撤回
        if(!Approval::isHaveAuth($id)){
            return response()->json(['code' => 500,'msg' => '已经进入流程,不能撤回']);
        }
        //删除数据case casedetail
        DB::beginTransaction();
        $caseDel = Approval::where("id",$id)->delete();
        $caseDetailDel = ApprovalDetail::where("approval_id",$id)->delete();
        if($caseDel && $caseDetailDel){
            DB::commit();
            return response()->json(['code' => 200,'msg' => '撤回成功']);
        }else{
            DB::rollback();
            return response()->json(['code' => 500,'msg' => '撤回失败,请重试']);
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
