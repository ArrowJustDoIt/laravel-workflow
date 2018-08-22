@extends('base')
@section('css')
    <style>
        .process_ul{
            margin-left: 30%;
        }
        .addProcess{
            font-size: 20px;
            color: #1E9FFF;
        }
    </style>
@endsection
@section('content')
    <div class="layui-tab layui-tab-brief">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-tab" style="margin-top: -10px">
                    <ul class="layui-tab-title">
                        <li class="layui-this">流程详情</li>
                        <li>表单详情</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <ul class="layui-timeline layui-form process_ul">
                                <li class="layui-timeline-item">
                                    <i class="layui-icon layui-timeline-axis layui-icon-add-circle" style="font-size: 20px;color: #a1d166;"></i>
                                    <div class="layui-timeline-content layui-text">
                                        <div class="layui-timeline-title">
                                            <span class="show_data">发起人</span>
                                        </div>
                                    </div>
                                </li>
                                @foreach($processData as $vo)
                                <li class="layui-timeline-item">
                                    <i class="layui-icon layui-timeline-axis layui-icon-ok-circle addProcess" style="color: #a1d166;"></i>
                                    <div class="layui-timeline-content layui-text">
                                        <div class="layui-timeline-title">
                                        <span class="show_data">
                                            {{$vo['process_name']}}({{$vo['process_user_name']}})<span style="margin-left: 10px"></span>
                                            @if($vo['status'] == '已同意')
                                            <font color="green">{{$vo['status']}}</font>
                                            @else
                                            <font color="red">{{$vo['status']}}</font>
                                            @endif
                                            【{{$vo['approval_detail_comment']}}】
                                        </span>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="layui-tab-item">
                            <form class="layui-form form-container" action="" method="post">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">审批类型</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="case_title" value="{{$flowData['flow_name']}}" required  lay-verify="required" class="layui-input" disabled>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">审批标题</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="case_title" value="{{$ApprovalData['title']}}" required  lay-verify="required" class="layui-input" disabled>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">审批备注</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="case_desc" value="{{$ApprovalData['desc']}}" required  lay-verify="required" class="layui-input" disabled>
                                    </div>
                                </div>
                                {!! $html !!}
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @endsection
