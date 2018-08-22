<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkflowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if($keyword = request()->input('keyword')){
            $workFlowRes = Workflow::where("flow_name",'like','%'.$keyword.'%')->get();
        }else{
            $workFlowRes = Workflow::get();
        }
        return view('workflow.index',['workFlowRes'=>$workFlowRes,'keyword'=>$keyword]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('workflow.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(),[
            'flow_name' => 'required',
            'flow_sort' => 'required|numeric',
        ],[
            'required' => ':attribute 为必填项',
            'numeric' => ':attribute 必须为数字',
        ], [
            'flow_name' => '名称',
            'flow_sort' => '排序',
        ])->validate();

        //入库
        $data = $request->only(['flow_name','flow_desc','flow_sort']);
        $insertRes = Workflow::create($data);
        if(!$insertRes){
            $request->session()->flash('error', '入库失败!');
            exit;
        }
        return redirect('admin/workflows')->with('success','入库成功');
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
        $data = Workflow::find($id);
        return view('workflow.edit',['id'=>$id,'data'=>$data]);
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
            Validator::make($request->all(),[
                'flow_name' => 'required',
                'flow_sort' => 'required|numeric',
            ],[
                'required' => ':attribute 为必填项',
                'numeric' => ':attribute 必须为数字',
            ], [
                'flow_name' => '名称',
                'flow_sort' => '排序',
            ])->validate();

            $data = $request->only(['flow_name','flow_desc','flow_sort']);
            $updateRes = Workflow::where("id",$id)->update($data);
            if($updateRes){
                return redirect()->action('Admin\WorkflowController@edit', ['id'=>$id])->with('success','更新成功');
            }else{
                return redirect()->action('Admin\WorkflowController@edit', ['id'=>$id])->with('error','更新失败');
            }
        }
    }

    public function change(Request $request){
        $id = $request->input('id');
        $workflowData = Workflow::find($id);

        //如果要开启,判断有没有设计表单和节点
        if(!$workflowData['flow_design']){
            return response()->json(['msg'=>'请设计表单后再开启该审批流']);
        }
        if(!$workflowData['flow_process']){
            return response()->json(['msg'=>'请设置节点后再开启该审批流']);
        }

        $data = [
            'flow_status' => ($workflowData['flow_status'] == 1) ? 2 : 1,
        ];
        $updateRes = Workflow::where('id',$id)->update($data);
        if($updateRes){
            return response()->json(['msg'=>'更新成功']);
        }else {
            return response()->json(['msg'=>'更新失败']);
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
