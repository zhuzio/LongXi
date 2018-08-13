@extends('layouts.app')
@section('title', '客户')
@section('style')
    @parent
    <style type="text/css">
        th{
            text-align: center!important;
        }
        td{
            text-align: center;
        }
    </style>
@endsection
@section('content')
    <div class="system-time"><p><i class="iconfont">&#xe6bb;</i><span class="nowTime"></span></p></div>
    {{--<div class="x-nav">
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
            <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>--}}
    <table class="layui-table" border="1" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th>账户id</th>
            <th>审核状态</th>
            <th>注册日期</th>
        </thead>
        <tbody>
        @foreach ($data as $list)
            <tr>
                <td>{{ $list->account }}</td>
                @if ($list->is_check == 0)
                    <td>待审核</td>
                @else
                    <td>已审核</td>
                @endif
                <td>{{ $list->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="page">
        <div>
            {{ $data->links() }}
        </div>
    </div>
    <script>
        setInterval(function(){
            var time=new Date();
            var year=time.getFullYear(); //获取年份
            var month=time.getMonth()+1; //获取月份
            var day=time.getDate();  //获取日期
            var hour=checkTime(time.getHours());  //获取时
            var minute=checkTime(time.getMinutes()); //获取分
            var second=checkTime(time.getSeconds()); //获取秒
            /****当时、分、秒、小于10时，则添加0****/
            function checkTime(i){
                if(i<10) return "0"+i;
                return i;
            }
            $(".nowTime").html(year+"-"+month+"-"+day+"-"+hour+":"+minute+":"+second);

        },1000);   //setInterval(fn,i) 定时器，每隔i秒执行fn

    </script>
@endsection