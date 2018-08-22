@extends('base')
@section('css')
    <style>
        .process-set{
            position: fixed;
            right: 50px;
            bottom: 50px;
        }
        .process-del{
            display: none;
            font-size: 20px;
            color: #b92c28;
            vertical-align: middle;
            margin-left: 3%;
        }
        .addProcess{
            font-size: 20px;
            color: #1E9FFF;
        }
    </style>
@endsection
@section('content')

    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>注意:节点设置后请勿随意改动,否则可能导致审批数据错误</legend>
    </fieldset>
    <form class="layui-form form-container" action="{{route('process.store')}}" method="post" style="margin-top: 3%;margin-left: 10%">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$id}}"/>
        <ul class="layui-timeline layui-form process_ul">
            <li class="layui-timeline-item">
                <i class="layui-icon layui-timeline-axis layui-icon-add-circle" style="font-size: 20px;color: #a1d166;"></i>
                <div class="layui-timeline-content layui-text">
                    <div class="layui-timeline-title">
                        <span class="show_data">发起人</span>
                    </div>
                </div>
            </li>
            @if(!empty($processData))
            @foreach($processData as $vo)
            <li class="layui-timeline-item">
                <i class="layui-icon layui-timeline-axis layui-icon-ok-circle addProcess" style="color: #a1d166;"></i>
                <div class="layui-timeline-content layui-text">
                    <div class="layui-timeline-title">
                        <span class="show_data">
                            {{$vo['process_name']}}({{$vo['process_user_name']}})
                            <i class="layui-icon layui-icon-close-fill process-del"></i>
                        </span>
                        <input type="hidden" class="post_data" name="post_data[]" value="{{$vo['process_name']}}-{{$vo['process_user']}}-{{$vo['process_user_name']}}"/>
                    </div>
                </div>
            </li>
            @endforeach
            @endif
            <li class="layui-timeline-item">
                <i class="layui-icon layui-timeline-axis layui-icon-add-circle addProcess"></i>
                <div class="layui-timeline-content layui-text">
                    <div class="layui-timeline-title">
                        <span class="show_data"></span>
                        <input type="hidden" class="post_data" name="post_data[]" value=""/>
                    </div>
                </div>
            </li>
        </ul>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn process-set layui-btn-primary" lay-submit lay-filter="*">设置节点</button>
            </div>
        </div>
    </form>
    <div class="layui-form layui-form-pane" id="process" style="display: none">
        <div class="layui-form-item" style="margin-top: 7%">
            <label class="layui-form-label">节点名</label>
            <div class="layui-input-block">
                <input type="text" name="process_name" value="" placeholder="请输入节点名" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">审批人</label>
            <div class="layui-input-block">
                <select name="process_user" id="process_user" lay-verify="" lay-search>
                    @foreach($allUser as $k => $vo)
                    <option value="{{$k}}">{{$vo}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @endsection
    @section('script')
        <script>
            //动态绑定鼠标移动显示事件
            $(document).on("mouseover",".layui-text",function(){
                $(this).find('.process-del').show();
            });
            $(document).on("mouseout",".layui-text",function(){
                $(this).find('.process-del').hide();
            });
            //动态绑定点击按钮删除元素事件
            $(document).on("click",".process-del",function(){
                $(this).parents('.layui-timeline-item').remove();
            });
            //动态绑定layer弹窗以及和父页面交互事件
            $(".process_ul").on("click", ".addProcess",function(){
                var addProcess = $(this);
                //如果有数据证明是修改,不生成html元素
                var process_name =  addProcess.next().find('.post_data').val();
                var isAdd_html = 0;
                if(process_name){
                    var isAdd_html = 1;
                }
                layer.open({
                    type: 1,
                    closeBtn: 1,
                    area: ['400px', '200px'],
                    title: false,
                    shadeClose: true,
                    btn: ['确定'],
                    content: $("#process"),
                    yes: function(index, layero){
                        //获取隐藏div数据
                        var process_name =  $("input[name='process_name']").val();
                        var process_user = $("#process_user").val();
                        var process_user_name = $("#process_user").find("option:selected").text();
                        if(!process_name || !process_user){
                            parent.layer.msg('请填写完整信息');
                            return;
                        }
                        //数据赋值
                        addProcess.next().find('.show_data').html(process_name + "(" + process_user_name + ")");
                        addProcess.next().find('.post_data').val(process_name + "-" + process_user + "-" + process_user_name);

                        if(isAdd_html == 0){
                            //更改图标
                            addProcess.removeClass('layui-icon-add-circle');
                            addProcess.addClass('layui-icon-ok-circle');
                            addProcess.css('color','#a1d166');
                            addProcess.next().find(".layui-timeline-title").append('<i class="layui-icon layui-icon-close-fill process-del"></i>');

                            //生成html元素
                            var insert_html = '<li class="layui-timeline-item">\n' +
                                '            <i class="layui-icon layui-timeline-axis layui-icon-add-circle addProcess"></i>\n' +
                                '            <div class="layui-timeline-content layui-text">\n' +
                                '                <div class="layui-timeline-title">\n' +
                                '                    <span class="show_data"></span>\n' +
                                '                    <input type="hidden" class="post_data" name="post_data[]" value=""/>\n' +
                                '                </div>\n' +
                                '            </div>\n' +
                                '        </li>';
                            $(".process_ul").append(insert_html);
                        }
                        //赋值后清空数据
                        $("input[name='process_name']").val('');


                        layer.close(index);
                    },
                    end: function () {
                        $('#process').css("display",'none');

                    }
                });
            });
        </script>
    @endsection
