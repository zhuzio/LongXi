@extends('layouts.app')
@section('title', '提现记录')
@section('style')
    @parent
    <style type="text/css">
        .shopCenterContainer{
            width: 100%;
            height: auto;
        }
        .shopCenterContainer ul{
            /*display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content:space-between;*/
        }
        .shopCenterContainer ul li{
            width: 240px;
            height: 355px;
            float: left;
        }
        .scImg{
            width: 100%;
        }
        .shopCenterContainer ul li:hover{
            border: 1px #51c2d4 solid;
            box-sizing: border-box;
            border-radius: 5px;
        }
        .shopCenterContainer ul li:hover .scName{
            color: #51c2d4;
        }
        .scImg img {
            display: block;
            width: 90%;
            height: auto;
            margin: 0 auto;
        }
        .scName{
            font-size: 16px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            width: 95%;
            margin: 10px auto;
            text-indent: 1em;
        }
        .scPrice{
            width: 95%;
            margin: 0 auto;
            height: auto;
            overflow: hidden;
        }
        .scPriceShow{
            font-size: 20px;
            color: #f9724e;
        }
        .scNumCtrl{
            display: inline-block;
            float: right;
            margin-right: 15px;
        }
        .scNumCtrl input{
            width: 40px;
            height: 25px;
            border-radius: 5px;
            border: solid 1px #d5d5d5;
            text-align: center!important;
            margin: 0 5px;
        }
        .numBtn{
            width: 20px;
            height: 20px;
            border-radius:50%;
            border: 1px #999999 solid;
            display: inline-block;
            /*text-align: center;*/
            /*line-height: 17px;*/
            cursor: pointer;
            background: none;
            font-size: 16px;
        }
        .numBtn:hover{
            color: #51c2d4;
            border: 1px #51c2d4 solid;
        }
        .scAD{
            width: 117px;
            height: 25px;
            background-color: #51c2d4;
            border-radius: 13px;
            font-size: 14px;
            font-weight: normal;
            font-stretch: normal;
            letter-spacing: 1px;
            color: #ffffff;
            border: none;
            cursor: pointer;
            margin: 10px auto;
            display: block;
        }
        .scAD:hover{
            color: #4950cc;
        }
    </style>
@endsection
@section('content')
    <div class="system-time"><p><i class="iconfont">&#xe6bb;</i><span class="nowTime"></span></p></div>
    {{--<div class="x-nav">
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
            <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>--}}
<div class="shopCenterContainer">
    <ul>
        @foreach($data as $list)
        <li>
            <div class="scImg">
                <img src="{{URL::asset($list->pic)}}"/>
            </div>
           <p class="scName">{{ $list->name }}</p>
            <div class="scPrice">
                <span class="scPriceShow">¥ <i>{{ $list->price }}</i></span>
                <p class="scNumCtrl">
                    <button class="numBtn sub">-</button><input type="number" readonly value="0"><button class="numBtn add ">+</button></p>
            </div>
            <button class="scAD">立即购买</button>
        </li>
        @endforeach
    </ul>
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

    /*
    * 商品增加
    * */
    $(".add").click(function () {
        var nowTotal = parseInt($(this).siblings("input").val());
        nowTotal = nowTotal + 1;
        $(this).siblings("input").val(nowTotal)
    });
    /*
    * 商品减少
    * */
    $(".sub").click(function () {
        var nowTotal = parseInt($(this).siblings("input").val());
        if (nowTotal == 0) {
            $(this).siblings("input").val(0)
        }else {
            nowTotal = nowTotal - 1;
            $(this).siblings("input").val(nowTotal)
        }
    });
    /*
    * 立即购买
    * */
    $(".scAD").click(function () {
        layer.prompt({title: '请输入支付密码', formType: 1}, function(pass, index){
            layer.close(index);
            $.ajax({
                url:"",
                type:"post",
                dataType:"json",
                data:{},
                success:function (e) {
                    console.log(e)
                },
                error:function (e) {
                    console.log(e)
                }
            });
        });
    })
</script>
@endsection