@extends('layouts.login')
@section('title', '会员登录')
@section('style')
    @parent
    <style type="text/css">
       .bgContainer{
           width: 100%;
           height: 100%;
           position: fixed;
           top: 0;
           left: 0;
           background: url("web/images/loginBG.png");
       }
       .container{
           background: none;
           height: 100%;
           overflow: hidden;
       }
        .loginContainer{
            width: 400px;
            height: 500px;
            position:fixed;
            left: 0;
            top: 0;
            bottom: 0;
            right: 0;
            margin: auto;
        }
        .loginList{
            width: 400px;
            border-bottom: 1px white solid;
            margin-bottom: 20px;
            height: 50px;
            line-height: 50px;

        }
       .logonLog{
           width: 100%;
           height:130px;
           background: url("web/images/login_logo.png") no-repeat center;
           background-size: auto 100%;
           margin-bottom: 80px;
       }
        .loginList i{
            font-size: 30px;
            color: white;
            margin-left: 15px;
        }
        .loginList >input{
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            width: 80%;
            float: right;
            height: 50px;
            line-height: 50px;
            font-weight: normal;
        }
       input::-webkit-input-placeholder { /* WebKit browsers */
           color: #CCCCCC;
       }
       input:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
           color:  #CCCCCC;
       }
       input::-moz-placeholder { /* Mozilla Firefox 19+ */
           color:  #CCCCCC;
       }
       input:-ms-input-placeholder { /* Internet Explorer 10+ */
           color:  #CCCCCC;
       }
       input[type=submit]{
           width: 100%;
           height: 45px;
           border-radius: 5px;
           font-size: 18px;
           color: white;
           background: rgb(77,187,205);
           border: none;
           cursor: pointer;
           margin-top: 50px;
       }
       @media screen and (max-width: 320px){
           .bgContainer{
               background: url("web/images/login_iphone_bg.png");
               background-size: 100%;
           }
           .logonLog{
               width: 100%!important;
               height: 80px!important;
               margin-bottom:30px!important;
           }
       }
       @media screen and (max-width: 768px){
          .bgContainer{
               background: url("web/images/login_iphone_bg.png") no-repeat center;
               background-size: 100%;
           }
           .loginContainer{
               width: 90%;
               height: 50%;
           }
           .logonLog{
               width: 100%;
               height: 100px;
               margin-bottom:50px;
           }
           .loginList {
               width: 90%;
               border-bottom: 1px white solid;
               margin:0 auto 20px;
               height: 30px;
               line-height: 30px;
               padding-bottom: 5px;
           }
           .loginList i {
               font-size:22px;
               color: white;
               margin-left: 15px;
           }
           .loginList >input {
               font-size: 16px;
               width: 80%;
               float: right;
               height: 30px;
               line-height: 30px;
               font-weight: normal;
           }
           input[type=submit]{
               display: block;
               width: 90%!important;
               height: 40px;
               margin:  25px auto 0;
           }
       }
    </style>
@endsection
@section('content')
    <div class="bgContainer">
        <div class="loginContainer">
            <div class="logonLog"></div>
            <form method="post" class="layui-form" >
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="loginList"><i class="iconfont">&#xe6b8;</i><input name="account" placeholder="用户名"   type="text" lay-verify="required" ></div>
                <div class="loginList"><i class="iconfont">&#xe82b;</i><input name="password" lay-verify="required" placeholder="密码"   type="password"></div>
                <input value="登录" lay-submit lay-filter="login" style="width:100%;" type="submit">
            </form>
        </div>
    </div>



    <script>
        $(function  () {
            layui.use('form', function(){
              var form = layui.form;
              // layer.msg('玩命卖萌中', function(){
              //   //关闭后的操作
              //   });
              //监听提交
              form.on('submit(login)', function(data){
                  $.ajax({
                      url:"/login",
                      type:"post",
                      dataType:"json",
                      data:data.field,
                      success:function (e) {
                          location.href='/index'
                      },
                      error:function (e) {
                          console.log(e)
                      }
                  });
                return false;
              });
            });
        })


    </script>


    <!-- 底部结束 -->
@endsection