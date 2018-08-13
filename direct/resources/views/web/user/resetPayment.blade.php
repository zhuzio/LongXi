@extends('layouts.app')
@section('title', '重置密码')
@section('style')
    @parent
    <style type="text/css">
        .cpContainer{
            width: 50%;
            height: 300px;
            margin: 30px auto;
        }
        .cpList{
            width: 400px;
            height: 60px;
            line-height: 60px;
            margin: 0 auto;
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
        .cpContainer >p{
            text-align: center;
            font-size: 18px;
            color:#51c2d4;
            margin-bottom: 30px;
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
        <p>修改支付密码</p>
        <div class="cpList"><span class="cpTitle">原始密码</span><input type="password" placeholder="请输入原始密码" class="originalPsd"></div>
        <div class="cpList"><span class="cpTitle">新密码</span><input type="password" placeholder="请输入新密码" class="newPsd"></div>
        <div class="cpList"><span class="cpTitle">确认密码</span><input type="password" placeholder="请再次输入新密码" class="cNewPsd"></div>
        <button class="cpBtn" onclick="cpBtn()">确认修改</button>
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
        *确认修改
        * */
        function cpBtn() {
            var _originalPsd = $(".originalPsd").val().toString(),
                _newPsd = $(".newPsd").val(),
                _cNewPsd = $(".cNewPsd").val();
            if (_newPsd !== _cNewPsd) {
                layer.msg('密码不一致', {icon: 5});
                return false;
            }else if(_originalPsd== '' || _newPsd == '' ||_cNewPsd == ''){
                layer.msg('密码不能为空', {icon: 5});
                return false;
            }else {
                $.ajax({
                    url:"/resetPayment",
                    type:"post",
                    dataType:"json",
                    data:{old_password:_originalPsd,password:_newPsd,password_confirmation:_cNewPsd},
                    success:function (e) {
                        layer.msg(e.msg);
                    },
                    error:function (e) {
                        console.log(e)
                    }
                });
            }
        }
    </script>
@endsection