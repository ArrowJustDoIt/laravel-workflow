@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>添加审批流</legend>
            </fieldset>
            <form class="layui-form" action="{{route('workflows.store')}}" method="post">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label class="layui-form-label">名　　称</label>
                    <div class="layui-input-block">
                        <input type="text" name="flow_name" value="" required  lay-verify="required" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描　　述</label>
                    <div class="layui-input-block">
                        <input type="text" name="flow_desc" value="" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">排　　序</label>
                    <div class="layui-input-block">
                        <input type="text" name="flow_sort" value="50" required  lay-verify="required" class="layui-input">
                    </div>
                </div>
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

@endsection
