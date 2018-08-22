<?php

namespace App\Http\Models;

class Workflow extends Base
{
    protected $table = 'workflows';
    protected $fillable = ['flow_name', 'flow_desc', 'flow_design', 'flow_process', 'flow_sort', 'flow_status'];

    /**
     * @desc 根据数据生成表单
     * @param $data         表单格式
     * @return string
     */
    public static function createTableHtmlByData($data){
        $html = "";
        foreach ($data as $k => $v){
            $required = ('yes' == $v['required']) ? 'required' : '';
            $dataValue = "";
            $html .= "<div class=\"layui-form-item\">
                        <label class=\"layui-form-label\">". $v['label'] ."</label>
                        <div class=\"layui-input-block\">";
            switch($v['field_type']){
                case 'text':
                    $html .= "<input type=\"text\" name=\"". $v['name'] ."\" value=\"".$dataValue."\" ". $required ." lay-verify=\"required\" class=\"layui-input\">";
                    break;
                case 'dropdown':
                    $selectOption = '';
                    foreach ($v['field_options']['options'] as $key => $value){
                        $selectSelect = $value['checked'] ? 'selected="selected"' : '';
                        $selectOption .= '<option value="'.$value['value'].'"'.$selectSelect.'>'. $value['label'] .'</option>';
                    }
                    $html .= "<select name=\"". $v['name'] ."\" ". $required ." lay-verify=\"required\">". $selectOption ."</select>";
                    break;
                case 'checkboxes':
                    foreach ($v['field_options']['options'] as $key => $value){
                        $html .= '<input type="checkbox" name="'. $v['name'] .'[]" lay-skin="primary" title="'. $value['label'] .'" checked="'.$value['checked'].'">';
                    }
                    break;
                case 'radio':
                    foreach ($v['field_options']['options'] as $key => $value){
                        $html .= '<input type="radio" name="'. $v['name'] .'[]" lay-skin="primary" title="'. $value['label'] .'" checked="'.$value['checked'].'">';
                    }
                    break;
                case 'date':
                    $html .= '<input type="text" name="'. $v['name'] .'"'. $required .' lay-verify="datetime" placeholder="yyyy-MM-dd HH:MM:SS" autocomplete="off" class="layui-input datetime">';
                    break;
                case 'email':
                    $html .= '<input type="text" name="'. $v['name'] .'"'. $required .' lay-verify="email" autocomplete="off" class="layui-input">';
                    break;
                default:
                    break;
            }
            $html .= "</div></div>";
        }
        return $html;
    }

    /**
     * @desc 根据数据生成表单
     * @param $data         表单格式
     * @param $fieldValue   表单数据
     * @return string
     */
    public static function createTableHtmlByFieldData($data,$fieldValue = array()){
        $html = "";
        foreach ($data as $k => $v){
            $required = ('yes' == $v['required']) ? 'required' : '';
            $dataValue = "";
            $html .= "<div class=\"layui-form-item\">
                        <label class=\"layui-form-label\">". $v['label'] ."</label>
                        <div class=\"layui-input-block\">";
            switch($v['field_type']){
                case 'text':
                    //如果有初始值传入
                    $dataValue = '';
                    if($fieldValue[$v['name']]){
                        $dataValue = $fieldValue[$v['name']];
                    }
                    $html .= "<input type=\"text\" name=\"". $v['name'] ."\" value=\"".$dataValue."\" ". $required ." lay-verify=\"required\" class=\"layui-input\">";
                    break;
                case 'dropdown':
                    $selectOption = '';
                    foreach ($v['field_options']['options'] as $key => $value){
                        $selectSelect = '';
                        //如果有初始值传入
                        if($fieldValue[$v['name']]){
                            if($value['value'] == $fieldValue[$v['name']]){
                                $selectSelect = 'selected="selected"';
                            }
                        }
                        $selectOption .= '<option value="'.$value['value'].'"'.$selectSelect.'>'. $value['label'] .'</option>';
                    }
                    $html .= "<select name=\"". $v['name'] ."\" ". $required ." lay-verify=\"required\">". $selectOption ."</select>";
                    break;
                case 'checkboxes':
                    foreach ($v['field_options']['options'] as $key => $value){
                        $isCheck = '';
                        if($fieldValue[$v['name']]){
                            //如果有初始值传入
                            if(isset($fieldValue[$v['name']][$key]) && $fieldValue[$v['name']][$key] == 'on'){
                                $isCheck = 'checked="checked"';
                            }
                        }
                        $html .= '<input type="checkbox" name="'. $v['name'] .'[]" lay-skin="primary" title="'. $value['label'] .'" '.$isCheck.'>';
                    }
                    break;
                case 'radio':
                    foreach ($v['field_options']['options'] as $key => $value){
                        $isCheck = '';
                        //如果有初始值传入
                        if(isset($fieldValue[$v['name']][$key]) && $fieldValue[$v['name']][$key] == 'on'){
                            $isCheck = 'checked="checked"';
                        }
                        $html .= '<input type="radio" name="'. $v['name'] .'[]" lay-skin="primary" title="'. $value['label'] .'" '.$isCheck.'>';
                    }
                    break;
                case 'date':
                    $dataValue = '';
                    if($fieldValue[$v['name']]){
                        $dataValue = $fieldValue[$v['name']];
                    }
                    $html .= '<input type="text" value="'.$dataValue.'" name="'. $v['name'] .'"'. $required .' lay-verify="datetime" placeholder="yyyy-MM-dd HH:MM:SS" autocomplete="off" class="layui-input datetime">';
                    break;
                case 'email':
                    $dataValue = '';
                    if($fieldValue[$v['name']]){
                        $dataValue = $fieldValue[$v['name']];
                    }
                    $html .= '<input type="text" value="'.$dataValue.'" name="'. $v['name'] .'"'. $required .' lay-verify="email" autocomplete="off" class="layui-input">';
                    break;
                default:
                    break;
            }
            $html .= "</div></div>";
        }
        return $html;
    }

    /**
     * @desc 获取流程数据,并拼接数据
     * @return mixed
     */
    public static function getFlowName(){
        $flowData = Workflow::get();
        foreach ($flowData as $k => $v){
            $returnData[$v['id']] = $v['flow_name'];
        }
        return $returnData;
    }

    /**
     * @desc 获取当前工作流程状态
     * @param $flow_id
     * @return bool
     */
    public static function getWorkflowStatus($flowId){
        $map = [
            "id"        =>  $flowId,
        ];
        $workFlowData = Workflow::where($map)->first();
        return $workFlowData['flow_status'];
    }
}
