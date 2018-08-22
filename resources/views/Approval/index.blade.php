@extends('base')

@section('content')
    <div class="layui-tab-item layui-show">

        <form class="layui-form layui-form-pane" action="{{route('approvals.index')}}" method="get">
            <div class="layui-inline">
                <label class="layui-form-label">关键词</label>
                <div class="layui-input-inline">
                    <input type="text" name="keyword" value="{{$keyword}}" placeholder="请输入关键词" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <button class="layui-btn">搜索</button>
            </div>
        </form>
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>审批列表</legend>
        </fieldset>
        <table class="layui-table">
            <thead>
            <tr>
                <th style="width: 30px;">id</th>
                <th>名称</th>
                <!--<th>类型</th>-->
                <th>描述</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($workflowRes as $vo)
            <tr>
                <td>{{$vo['id']}}</td>
                <td>{{$vo['flow_name']}}</td>
                <td>{{$vo['flow_desc']}}</td>
                <td>
                    <a href="{{route('approvals.create',['id'=>$vo['id']])}}" class="layui-btn layui-btn-primary layui-btn-sm">发起审批</a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <!--分页-->
        {{ $workflowRes->links() }}
    </div>

    @endsection
