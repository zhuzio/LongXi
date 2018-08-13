@extends('layouts.app')
@section('title', '礼品积分转换')
@section('style')
    @parent
    <style type="text/css">
        .cpContainer{
            width: 50%;
            height: 300px;
            margin: 30px auto;
        }
        .cpContainer >p{
            text-align: center;
            font-size: 15px;
            color: #1AA093;
            margin: 20px auto;
        }
        .cpList{
            width: 400px;
            height: 40px;
            line-height: 40px;
            margin: 0 auto;
            font-size: 18px;
        }
        .cpTitle{
            display: inline-block;
            width: 35%;
            text-align: right;
            font-size: 16px;
        }
        .cpList input{
            width: 45%;
            height: 40px;
            margin-left: 3%;
            border: none;
            border-bottom: 1px solid #b4b4b4;
            padding-left: 5px;
            font-size: 16px;
        }
        .cpBtn{
            display: block;
            border: none;
            width: 60%;
            height: 45px;
            margin: 30px auto 0;
            border-radius: 5px;
            color: white;
            background: #51c2d4;
            font-size: 18px;
        }
        .capImgs{
            border-radius: 5px!important;
            border: 1px solid #e5e5e5!important;
            margin-left: 10px;
            width: 28%;
            height: 90%;
            display: inline-block;
        }
        .cpList >i{
            font-style: normal;
            margin-left: 15px;
            font-size: 20px;
            color: #3d899a;
        }
        input::-webkit-outer-spin-button,input::-webkit-inner-spin-button{
            -webkit-appearance:textfield;
        }
        input[type="number"]{
            -moz-appearance:textfield;
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
        <p>礼品积分转换</p>
        <div class="cpList"><span class="cpTitle">总礼品积分</span><i>{{ $data['total'] }}</i></div>
        {{--<div class="cpList"><span class="cpTitle">最低转换积分</span><i>100</i></div>--}}
        <div class="cpList"><span class="cpTitle">转换</span><input type="number" placeholder="输入需转换积分" class="cNum"></div>
        <div class="cpList"><span class="cpTitle">图片验证码</span><input type="number" placeholder="图片验证码" class="z_tel z_img"><img src="{{ captcha_src() }}" onclick="this.src='{{ captcha_src() }}?r='+Math.random();" alt="" class="capImgs"></div>
        <button class="cpBtn" onclick="cpBtn()">确认转换</button>
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

        function cpBtn() {
            var _cNum = $(".cNum").val(),
                cpt = $('.z_img').val();
            if (_cNum == '') {
                layer.msg('转换积分不能为空', {icon: 5});
                return false;
            }
            if (_cNum%100 !== 0){
                layer.msg('转化数量必须为100的整倍数', {icon: 5});
                return false;
            }
            if (cpt == '') {
                layer.msg('图片验证码必须', {icon: 5});
                return false;
            }
            $.ajax({
                url:"/transformAddedPoints",
                type:"post",
                dataType:"json",
                data:{points:_cNum,cpt:cpt},
                success:function (e) {
                    layer.msg(e.msg);
                },
                error:function (e) {
                    console.log(e)
                }
            });
        }
    </script>
@endsection