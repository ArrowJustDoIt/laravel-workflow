@extends('base')
@section('content')
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <table class="layui-table">
                <thead>
                <tr>
                    <th style="width: 30px;">id</th>
                    <th>申请类别</th>
                    <th>申请标题</th>
                    <th>当前节点</th>
                    <th>当前审批人</th>
                    <th>审批意见</th>
                    <th>当前状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($myApprovalList as $vo)
                <tr>
                    <td>{{$vo['id']}}</td>
                    <td>{{$vo['flow_name']}}</td>
                    <td>{{$vo['title']}}</td>
                    <td>{{$vo['process_name']}}</td>
                    <td>{{$vo['process_user_name']}}</td>
                    <td>{{$vo['approval_detail_comment']}}</td>
                    <td>@if($vo['flow_status'] == 2)
                        <font color="red">已禁止</font>
                        @else
                        @if(($vo['approval_detail_status'] == '已驳回') or ($vo['approval_detail_status'] == '已拒绝'))
                        <font color="red">{{$vo['approval_detail_status']}}</font>
                        @else
                        <font color="green">{{$vo['approval_detail_status']}}</font>
                        @endif
                        @if($vo['status'] == '1')
                        <font color="red">流程中</font>
                        @else
                        <font color="green">已完结</font>
                        @endif
                        @endif
                    </td>
                    <td>
                        <a href="javascript:;" onclick="layer_show('查看申请详情','{{route('approvals.show',['id'=>$vo['id']])}}','850','500')" class="layui-btn layui-btn-primary layui-btn-sm">详情</a>
                        @if($vo['is_auth'] == true)
                        <a href="javascript:;" onclick="layer_show('修改申请详情','{{route('approvals.edit',['id'=>$vo['id']])}}','850','500')" class="layui-btn layui-btn-primary layui-btn-sm">修改</a>
                        <a href="javascript:;" data-id="{{$vo['id']}}" class="layui-btn layui-btn-danger layui-btn-sm myapprovalrollback">撤回</a>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <!--分页-->
            {{$approvalRes->links()}}
        </div>
    </div>

    @endsection
    @section('script')
    <script>
        //撤回申请
        $(".myapprovalrollback").on('click',function(){
            var r =confirm("是否要撤回该申请，撤回后不可恢复");
            if(r){
                var case_id = $(this).data('id');
                $.post("{{route('approvals.rollback')}}",{id:case_id},function(res){
                    layer.msg(res['msg']);
                    setInterval('window.location.href="{{route('approvals.lists')}}"',2000);
                })
            }
        });
    </script>
    @endsection
