@extends('base')

@section('content')
    <div class="layui-tab layui-tab-brief">
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <form class="layui-form form-container" action="{{route('workflows.update',['id'=>$id])}}" method="post">
                    {{csrf_field()}}
                    {{ method_field('put') }}
                    <input type="hidden" name="id" value="{{$id}}"/>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审批名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="flow_name" value="{{$data['flow_name']}}" required  lay-verify="required" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审批描述</label>
                        <div class="layui-input-block">
                            <input type="text" name="flow_desc" value="{{$data['flow_desc']}}" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">审批排序</label>
                        <div class="layui-input-block">
                            <input type="text" name="flow_sort" value="{{$data['flow_sort']}}" required  lay-verify="required" class="layui-input">
                        </div>
                    </div>
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