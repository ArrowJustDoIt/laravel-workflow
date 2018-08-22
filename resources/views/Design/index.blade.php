@extends('base')

@section('css')
<link rel="stylesheet" href="/static/plugins/formbuilder/vendor.css" />
<link rel="stylesheet" href="/static/plugins/formbuilder/formbuilder.css" />
<style>
* {
    box-sizing: border-box;
}

body {
    background-color: #d6d3d3;
    font-family: sans-serif;
}

.fb-main {
    background-color: #fff;
    border-radius: 5px;
    min-height: auto;
}

input[type=text] {
    height: 26px;
    margin-bottom: 3px;
}

select {
    margin-bottom: 5px;
    font-size: 40px;
}
</style>
@endsection
@section("content")
<div class="page-container">
    <div class='fb-main'></div>
</div>
<input name='flow_design' id='flow_design' value='{{$flow_design}}' type='hidden'>
<input name='id' id='id' value='{{$id}}' type='hidden'>
@endsection
@section("script")
<script src="/static/plugins/formbuilder/vendor.js"></script>
<script src="/static/plugins/formbuilder/formbuilder.js"></script>
<script>
$(function() {
    fb = new Formbuilder({
        selector: '.fb-main',
        bootstrapData: {!! $flow_design !!}
    });
    fb.on('save', function(payload) {
        $('#flow_design').val(payload);
    });
    $("#up").click(function() {
        var flow_design = $("#flow_design").val();
        var id = $("#id").val();
        $.ajax({
            url: '{{route("designs.store")}}',
            data: { flow_design: flow_design, id: id },
            type: 'post',
            cache: true,
            dataType: 'json',
            success: function(data) {
                parent.layer.msg(data['msg']);
                if (data['code'] == 200) {
                    setTimeout(function() {
                        parent.location.reload();
                    }, 1000)
                }
            },
            error: function(data) {
                if(data){
                    layer.msg(data.responseJSON.errors.flow_design[0]);
                }

            }
        });

    })
});
</script>
@endsection