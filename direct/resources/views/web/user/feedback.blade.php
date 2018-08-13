@extends('layouts.app')
@section('title', '信息反馈')
@section('style')
    @parent
    <style type="text/css">
        .feedbackContainer{
            width: 500px;
            height: auto;
            /*background: #92dfff;*/
            margin: 50px auto;
        }
        .fbTitle{
            font-size: 16px;
            margin-bottom: 10px;
        }
        .fbTel{
            border: 1px #CCCCCC solid;
            border-radius: 5px;
            width: 60%;
            height: 40px;
            margin:  0px 0px 20px 0;
            padding-left: 10px;
            font-size: 14px;
        }
        textarea{
            resize: none;
            width: 94%;
            height: 200px;
            border-radius: 5px;
            font-size: 15px;
            padding: 10px 3%;
        }
        .fbImg{
            width: 100px;
            min-height: 100px;
            background: url("web/images/shangchaun.png") no-repeat center;
            background-size: 60%;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .fbImg >img{
            display: block;
            max-width: 100%;
            height: auto;
        }
        .fbImg >input{
            display: block;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }
        button{
            display: block;
            border: none;
            width: 80%;
            height: 45px;
            margin: 30px auto 0;
            border-radius: 5px;
            color: white;
            background: #51c2d4;
            font-size: 18px;
            cursor: pointer;
        }
        @media screen and (max-width: 768px){
            .feedbackContainer{
                width: 90%;
                font-size: 14px;
            }
            textarea{
                width: 90%;
                height: 100px;
            }
        }
    </style>
@endsection
@section('content')
    <div class="system-time"><p><i class="iconfont">&#xe6bb;</i><span class="nowTime"></span></p></div>
    {{--<div class="x-nav">
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
            <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>--}}
    <div class="feedbackContainer">
        <div>
            <p class="fbTitle">您的联系方式：</p>
            <input type="tel" class="fbTel" placeholder="请输入您的电话号码">
        </div>
        <div>
            <p class="fbTitle">意见及反馈：</p>
            <textarea name="" id="" class="fbTxt"></textarea>
        </div>
        <div>
            <p class="fbTitle">附加图片（选填）</p>
            <div class="fbImg">
                <img src="" alt="" id="fbImg">
                <input type="file" onchange="upImg(this.files)">
            </div>
        </div>
        <button class="fbBtn" onclick="pushFB()">提交</button>
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

        var imgSrc;
        function upImg(w) {
            var file = w[0];
            //创建读取文件的对象
            var reader = new FileReader();
            //创建文件读取相关的变量
            var imgFile;
            //为文件读取成功设置事件
            reader.onload=function(e) {
                imgFile = e.target.result;
                $("#fbImg").attr("src",imgFile);
                imgSrc = imgFile;
            }
            //正式读取文件
            reader.readAsDataURL(file);
        };
        function pushFB() {
            var tel = $(".fbTel").val(),
                fbTxt = $(".fbTxt").val();
            if (tel == ""){
                layer.msg('电话号码不能为空', {icon: 5});
                return false;
            };
            if (fbTxt == ""){
                layer.msg('说点您想说的吧,不能为空啊', {icon: 5});
                return false;
            };
            $.ajax({
                url:"/feedback",
                dataType:"json",
                type:"post",
                data:{

                },
                success:function (e) {
                    console.log(e)
                },
                error:function (e) {
                    console.log(e)
                }
            })
        }
    </script>
@endsection