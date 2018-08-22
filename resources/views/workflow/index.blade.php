@extends('base')

@section('content')
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <form class="layui-form layui-form-pane" action="{{route('workflows.index')}}" method="get">
                <div class="layui-inline">
                    <label class="layui-form-label">关键词</label>
                    <div class="layui-input-inline">
                        <input type="text" name="keyword" value="{{$keyword}}" placeholder="请输入关键词" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn">搜 索</button>
                    <a class="layui-btn layui-btn-primary" href="{{route('workflows.create')}}">新 增</a>
                </div>
            </form>

            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>审批流列表</legend>
            </fieldset>
            <table class="layui-table">
                <thead>
                <tr>
                    <th style="width: 30px;">id</th>
                    <th>名称</th>
                    <th>描述</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th>入库时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($workFlowRes as $vo)
                <tr>
                    <td>{{$vo->id}}</td>
                    <td>{{$vo->flow_name}}</td>
                    <td>{{$vo->flow_desc}}</td>
                    <td>{{$vo->flow_sort}}</td>
                    <td>
                        <input type="checkbox" class="switch-component" data-id="{{$vo['id']}}" @if($vo['flow_status'] == 1)checked="checked"@endif>
                    </td>
                    <td>{{$vo['updated_at']}}</td>
                    <td>{{$vo['created_at']}}</td>
                    <td>
                        <a href="javascript:;" onclick="layer_show('表单设计','{{route('designs.index',['id' => $vo['id']])}}','850','500')" class="layui-btn layui-btn-primary layui-btn-sm">表单设计</a>
                        <a href="javascript:;" onclick="layer_show('表单预览','{{route('designs.show',['id' => $vo['id']])}}','850','500')" class="layui-btn layui-btn-primary layui-btn-sm">表单预览</a>
                        <a href="javascript:;" onclick="layer_show('设置节点','{{route('process.index',['id' => $vo['id']])}}','850','500')" class="layui-btn layui-btn-primary layui-btn-sm">设置节点</a>
                        <a href="javascript:;" onclick="layer_show('修改','{{route('workflows.edit',['id' => $vo['id']])}}','850','500')" class="layui-btn layui-btn-primary layui-btn-sm">修改</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @endsection
    @section('script')
    <script>
        //更改角色状态
        $(".switch-component").on('click',function(){
            var flow_id = $(this).data('id');
            var status  = $(this).is(':checked') ? true : false;
            $.post("{{route('workflows.change')}}",{id:flow_id,status:status},function(res){
                layer.msg(res['msg']);
                setInterval('window.location.href= "{{route('workflows.index')}}"',2000);
            })
        });
    </script>
    @endsection
