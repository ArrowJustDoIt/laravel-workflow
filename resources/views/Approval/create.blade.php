@extends('base')

@section('content')
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <form class="layui-form form-container" action="{{route('approvals.store')}}" method="post">
                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$id}}"/>
                <div class="layui-form-item">
                    <label class="layui-form-label">审批标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="case_title" value="" required  lay-verify="required" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">审批备注</label>
                    <div class="layui-input-block">
                        <input type="text" name="case_desc" value="" required  lay-verify="required" class="layui-input">
                    </div>
                </div>
                {!! $html !!}
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="*">添加</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
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