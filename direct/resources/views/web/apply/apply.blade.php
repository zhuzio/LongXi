@extends('layouts.app')
@section('title', '申请服务中心')
@section('style')
    @parent

@endsection
@section('content')
    <button onclick="apply()">申请服务中心</button>
    <script>
        function apply(){
            $.ajax({
                url:"/agree",
                type:"post",
                dataType:"json",
                success:function (e) {
                    layer.msg(e.msg);
                },
                error:function (e) {
                    console.log(e)
                }
            });
        };
    </script>
@endsection