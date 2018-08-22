<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Models\Workflow;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DesignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = intval($request->input("id"));
        //查询
        $flowDesign = Workflow::find($id);
        $flowDesign = json_decode($flowDesign['flow_design'],true);
        $flowDesign = $flowDesign ? json_encode($flowDesign['fields']) : '[]';

        return view('design.index',['id'=>$id,'flow_design'=>$flowDesign]);
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
        if($request->isMethod('post')) {
            Validator::make($request->all(), [
                'id' => 'required',
                'flow_design' =>  [
                    'required',
                    function($attribute, $value, $fail) {
                        $jsonTemp = json_decode($value, true);
                        if (empty($jsonTemp)) {
                            return $fail('请设计表单后再上传');
                        }
                        return true;
                    },
                ]
            ], [
                'required' => ':attribute 为必填项',
            ], [
                'id' => '名称',
                'flow_design' => '表单设计'
            ])->validate();
            //更新
            $flowDesign = $request->input('flow_design');
            $id = $request->input('id');
            $jsonTemp = json_decode($flowDesign,true);
            //加上fields
            if(!isset($jsonTemp['fields'])){
                $jsonTempNew['fields'] = $jsonTemp;
                $flowDesign = json_encode($jsonTempNew);
            }elseif(empty($jsonTemp['fields'])){
                $request->session()->flash('error', '请设计表单后再上传!');
                return '';
            }
            $data = [
                'flow_design'   => $flowDesign,
            ];
            $updateRes = Workflow::where("id",$id)->update($data);
            if(!$updateRes){
                return response()->json(array('code'=>200,'msg'=>'入库失败'));
            }
            return response()->json(array('code'=>200,'msg'=>'入库成功'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $flowDesign = Workflow::find($id);
        if(!$flowDesign['flow_design']){
            return '';
        }
        $formDesign = json_decode($flowDesign['flow_design'],true);
        $html = Workflow::createTableHtmlByData($formDesign['fields']);
        return view("design.show",['html'=>$html]);
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
    public function update(Request $request, $id)
    {
        //
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
