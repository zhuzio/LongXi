@extends('layouts.app')
@section('title', '积分转让处理')
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
            margin-bottom: 5px;
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
            cursor: pointer;
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
        .getMsg{
            width: 25%;
            font-size: 12px;
            margin-left: 2%;
            cursor: pointer;
            height: 30px;
            border: 1px #51c2d4 solid;
            border-radius: 5px;
            color:  #51c2d4;
            background: none;
        }
        .z_pic{
            display: inline-block;
            width: 25%;
            font-size: 12px;
            margin-left: 2%;
            cursor: pointer;
            height: 30px;
            border: 1px #e5e5e5 solid;
            border-radius: 5px;
        }
        .z_tel{
            width: 20%!important;
        }
        .capImgs{
            border-radius: 5px!important;
            border: 1px solid #e5e5e5!important;
            margin-left: 10px;
            width: 28%;
            height: 90%;
            display: inline-block;
        }
        .cpList select {
            text-align: center;
            height: 30px;
            width: 35%;
            border-radius: 5px;
            border: solid 1px #d5d5d5;
            margin-left: 10px;
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
        <p>积分转账</p>
        <div class="cpList"><span class="cpTitle">总积分</span><i>{{ $data->assets->electronic }}</i></div>
        {{--<div class="cpList"><span class="cpTitle">最低转赠积分</span><i>1000</i></div>--}}
        <div class="cpList"><span class="cpTitle">选择转出类型</span>
            <select id="transfer_to">
                <option value="electronic">积分</option>
                <option value="shop_points">购物积分</option>
            </select>
        </div>
        <div class="cpList"><span class="cpTitle">转入账号</span><input type="text" placeholder="输入转入账号" class="z_Count"></div>
        <div class="cpList"><span class="cpTitle">转入积分</span><input type="number" placeholder="输入需转账积分" class="cNum"></div>
        <div class="cpList"><span class="cpTitle">短信验证码</span><input type="number" placeholder="短信验证码" class="z_tel z_Phone"><button class="getMsg" onclick="getPhoneReg()">获取短信码</button></div>
        <div class="cpList"><span class="cpTitle">图片验证码</span><input type="number" placeholder="图片验证码" class="z_tel z_img"><img src="{{ captcha_src() }}" onclick="this.src='{{ captcha_src() }}?r='+Math.random();" alt="" class="capImgs"></div>
        <div class="cpList"><span class="cpTitle">输入支付密码</span><input type="password"  placeholder="请输入支付密码" class="payment"></div>
        <button class="cpBtn" onclick="cpBtn()">确认转赠</button>
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
       * 获取短信验证码
       * */
        var isClick = true ;
        function getPhoneReg() {
            if (isClick) {
                isClick = false;
                var num = 120,
                    time=setInterval(function () {
                        num--;
                        if (num == 0){
                            clearInterval(time);
                            $(".getMsg").html("获取验证码").css({
                                color:"#51c2d4",
                                borderColor:"#51c2d4"
                            });;
                            isClick = true;
                        } else {
                            $(".getMsg").html(num+ "s后重发").css({
                                color:"#ccc",
                                borderColor:"#ccc"
                            });
                        }
                    },1000);
                // 发送短信请求
                /*$.ajax({
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
                });*/
            }
        }
        function cpBtn() {
            var _cNum = $(".cNum").val(),
                _z_Count = $(".z_Count").val(),
                transfer_to = $('#transfer_to option:selected').val(),
                cpt = $('.z_img').val(),
                payment = $('.payment').val();
            if (_z_Count == '') {
                layer.msg('转入账号不能为空', {icon: 5});
                return false;
            }
            if (_cNum == '') {
                layer.msg('转账积分不能为空', {icon: 5});
                return false;
            }
            if (_cNum % 1000 !==0) {
                layer.msg('转账积分只能为1000的整倍数', {icon: 5});
                return false;
            }
            if (payment == '') {
                layer.msg('支付密码必填', {icon: 5});
                return false;
            }
            if (cpt == '') {
                layer.msg('图片验证码必须', {icon: 5});
                return false;
            }
            if (transfer_to == '') {
                layer.msg('转账类型必须选择', {icon: 5});
                return false;
            }
            /*
            * 查询账号是否存在  账号 值：_z_Count
            * */
            $.ajax({
                url:"/transfer/checkUser",
                type:"post",
                dataType:"json",
                data:{account:_z_Count},
                success:function (e) {
                    layer.msg('该用户是'+e.data+',确定转入？', {
                        time: 0 //不自动关闭
                        ,btn: ['确定', '取消']
                        ,yes: function(index){
                            layer.close(index);
                            /*
                            *
                            * 开始转入 数量值：_cNum
                            * */
                            $.ajax({
                                url:"/electronicTransfer",
                                type:"post",
                                dataType:"json",
                                data:{to:_z_Count,money:_cNum,transfer_to:transfer_to,password:payment,cpt:cpt},
                                success:function (e) {
                                    layer.msg(e.msg);
                                },
                                error:function (e) {
                                    console.log(e)
                                }
                            });
                        }
                    });
                },
                error:function (e) {
                    console.log(e)
                }
            });
        }
    </script>
@endsection