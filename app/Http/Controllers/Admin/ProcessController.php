<?php

namespace App\Http\Controllers\admin;

use App\Http\Models\Workflow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = intval($request->input('id'));
        //获取当前流程审批节点
        $processData = Workflow::find($id);
        $processData = json_decode($processData['flow_process'],true);
        return view("process.index",['processData'=>$processData,'id'=>$id,'allUser'=>self::$userArr]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = intval($request->input('id'));
        if($request->isMethod('post')){
            $postData = $request->input("post_data");
            if(count($postData) <= 1){
                return redirect()->action('Admin\ProcessController@index', ['id'=>$id])->with('error','数据错误');
            }

            //入库
            $nowTime = time();
            foreach ($postData as $k => $v){
                if($v){
                    $tempProcess = explode('-',$v);
                    $processName = $tempProcess[0];
                    $processUser = $tempProcess[1];
                    $processUserName = $tempProcess[2];
                    $processArr[] = [
                        'process_user'      => $processUser,
                        'process_name'      => $processName,
                        'process_user_name' => $processUserName,
                    ];

                }
            }
            $processJson = json_encode($processArr);
            $saveData = [
                'flow_process'  => $processJson,
            ];
            $updateRes = Workflow::where("id",$id)->update($saveData);
            if($updateRes){
                return redirect()->action('Admin\ProcessController@index', ['id'=>$id])->with('success','入库成功');
            }else{
                return redirect()->action('Admin\ProcessController@index', ['id'=>$id])->with('error','入库失败');
            }
        }
    }

}
