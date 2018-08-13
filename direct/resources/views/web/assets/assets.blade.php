@extends('layouts.app')
@section('title', '我的账户')
@section('style')
    @parent
    <style type="text/css">
        table{
            margin: 50px auto 0;
            border: solid 1px #e5e5e5;
            width: 80%;
        }
        tr{
            height: 40px;
            text-align: center;
            font-size: 14px;
        }
        tr >td:nth-child(1){
            border-bottom: 1px #e5e5e5 solid;
            text-align: center;
            /*padding-right: 30px;*/
            background: #fafafa;
        }
        tr >td:nth-child(2){
            text-align: center;
            /*padding-left: 30px;*/
            border-bottom: 1px #e5e5e5 solid;
        }
        .cpT{
            color: #666;
        }

    </style>
@endsection
@section('content')
    <div class="system-time"><p><i class="iconfont">&#xe6bb;</i><span class="nowTime"></span></p></div>
    {{--<div class="x-nav">
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
            <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>--}}

    <div class="cpContainer">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="cpT">礼品积分</td>
                <td>{{ $data->advert_points }}</td>
            </tr>
            <tr>
                <td class="cpT">增值积分</td>
                <td>{{ $data->added_points }}</td>
            </tr>
            <tr>
                <td class="cpT">购物积分</td>
                <td>{{ $data->shop_points }}</td>
            </tr>
            <tr>
                <td class="cpT">积分</td>
                <td>{{ $data->electronic }}</td>
            </tr>
            <tr>
                <td class="cpT">福利积分</td>
                <td>{{ $data->repeat_consum }}</td>
            </tr>
        </table>
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