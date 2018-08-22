@extends('base')
@section('content')
    <div class="layui-tab layui-tab-brief">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <form class="layui-form form-container" action="{{route('approvals.update',['id'=>$id])}}" method="post">
                    {{ method_field('put') }}
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="{$id}"/>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审批类型</label>
                        <div class="layui-input-block">
                            <input type="text" value="{{$flowData['flow_name']}}" class="layui-input" disabled>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审批标题</label>
                        <div class="layui-input-block">
                            <input type="text" name="title" value="{{$ApprovalData['title']}}" required  lay-verify="required" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审批备注</label>
                        <div class="layui-input-block">
                            <input type="text" name="desc" value="{{$ApprovalData['desc']}}" required  lay-verify="required" class="layui-input">
                        </div>
                    </div>
                    {!! $html !!}
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="*">修改</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endsection
    @section('script')
        <script>
            layui.use('laydate', function(){
                var laydate = layui.laydate;
                //同时绑定多个
                lay('.datetime').each(function(){
                    laydate.render({
                        elem: this
                        ,trigger: 'click'
                    });
                });
            });
        </script>
    @endsection
