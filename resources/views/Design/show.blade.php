@extends('base')

@section("content")
<div class="layui-tab layui-tab-brief">
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <form class="layui-form form-container" action="#" method="post">
                {!! $html !!}
            </form>
        </div>
    </div>
</div>
@endsection