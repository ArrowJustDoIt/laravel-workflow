@extends('base')

@section('content')
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">

            <form class="layui-form layui-form-pane" action="{{route('applys.index')}}" method="get">
                <div class="layui-inline">
                    <label class="layui-form-label">审批类型</label>
                    <div class="layui-input-inline">
                        <select name="flow_id" lay-verify="" lay-search>
                            <option value="0">请选择</option>
                            @foreach($flowNameData as $key=>$vo)
                            <option value="{{$key}}" @if(!empty($flow_id) && ($flow_id == $key)) selected @endif>{{$vo}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn">搜索</button>
                </div>
            </form>
            <hr>
            <table class="layui-table">
                <thead>
                <tr>
                    <th style="width: 30px;">id</th>
                    <th>审批类型</th>
                    <th>申请人</th>
                    <th>审批标题</th>
                    <th>备注</th>
                    <th>发起时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($toDoListArr as $vo)
                <tr>
                    <td>{{$vo['id']}}</td>
                    <td>{{$vo['flow_name']}}</td>
                    <td>{{$vo['u_cn_name']}}</td>
                    <td>{{$vo['title']}}</td>
                    <td>{{$vo['desc']}}</td>
                    <td>{{$vo['created_at']}}</td>
                    <td>
                        <a href="javascript:;" onclick="layer_show('查看申请详情','{{route('approvals.show',['id'=>$vo['id']])}}','850','500')" class="layui-btn layui-btn-primary layui-btn-sm">查看详情</a>
                        <a href="javascript:;" data-id="{{$vo['id']}}" data-type="4" class="layui-btn layui-btn-normal layui-btn-sm approvaltodoupdate">同意</a>
                        <a href="javascript:;" data-id="{{$vo['id']}}" data-type="2" class="layui-btn layui-btn-danger layui-btn-sm approvaltodoupdate">驳回</a>
                        <a href="javascript:;" data-id="{{$vo['id']}}" data-type="3" class="layui-btn layui-btn-danger layui-btn-sm approvaltodoupdate">拒绝</a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <!--分页-->
            {{$toDoList->links()}}
        </div>
    </div>
    @endsection
    @section('script')
    <script>
        //批复
        $(".approvaltodoupdate").on('click',function(){
            var initCommon = $(this).text();
            var case_id = $(this).data('id');
            var case_type = $(this).data('type');
            layer.prompt({
                formType: 3,
                value: initCommon,
                title: '请输入审批意见',
                area: ['300px', '150px'] //自定义文本域宽高
            }, function(value, index, elem){
                $.post("{{route('applys.judge')}}",{id:case_id,type:case_type,value:value},function(res){
                    layer.msg(res['msg']);
                    setInterval('window.location.href= "{{route('applys.index')}}"',2000);
                });
                layer.close(index);
            });
        });
    </script>
    @endsection
