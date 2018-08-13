@extends('layouts.app')
@section('title', '客户注册')
@section('style')
    @parent
    <style type="text/css">
        body{
            /*overflow: scroll;*/
        }
        .mrContainer{
            width: 710px;
            text-align: center;
            /*background: #ff8e65;*/
            margin: 0 auto;
            overflow: hidden;
            margin-top: 20px;
        }
        .mrList{
            width: 100%;
            margin-bottom: 10px;
            /*background: #FFB800;*/
            height: 35px;
            line-height:35px;
            font-size: 16px;
        }
        .mrTitle{
            width: 30%;
            display: inline-block;
            height: 100%;
            text-align: right;
            float: left;
            margin-right: 10px;
        }
        .mrTitle i{
            color: #f9724e;
            font-size: 22px;
        }
        .mrList input[type=text],
        .mrList input[type=password],
        .mrList input[type=number],
        .mrList select{
            float: left;
            height: 30px;
            width: 33%;
            border-radius: 5px;
            border: solid 1px #d5d5d5;
            padding-left: 2%;
        }
        .mrList input[type=number]::-webkit-textfield-decoration-container {
            background-color: #fff;
        }    /*添加背景色 下边两行是去掉input 输入框右边的上下箭头按钮*/
        .mrList input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }
        .mrList input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
        }
        .mrList button{
            display: inline-block;
            width: 30%;
            height: 33px;
            border-radius: 5px;
            border: solid 1px #51c2d4;
            font-size: 15px;
            color: #51c2d4;
            background: white;
            cursor: pointer;
        }
        .mrList >img{
            display: inline-block;
            width: 20%;
            height: 33px;
            border-radius: 5px;
            cursor: pointer;
            border: 1px #d3d3d3 solid;
        }
        .mrList1 >select{
            display: inline-block;
            width: 20%;
            margin-right: 1%;
        }
        .mrList u {
            font-size: 12px;
            color: #cc2626;
            text-decoration: none;
            display: inline-block;
            width: 30%;
            word-wrap: break-word;
        }
    </style>
@endsection
@section('content')
    <div class="system-time"><p><i class="iconfont">&#xe6bb;</i><span class="nowTime"></span></p></div>
    <div class="x-nav">
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
            <i class="layui-icon" style="line-height:30px">ဂ</i></a>
    </div>
    <div class="mrContainer">
        <div class="mrList"><span class="mrTitle">注册账号<i>*</i></span><input type="text" value="{{ $data['account'] }}" class="regAccount" readonly></div>
        {{--<div class="mrList"><span class="mrTitle">客户类型<i>*</i></span>
            <select name="" id="" class="level">
                <option value="1">代理商</option>
                <option value="3">经销商</option>
            </select>
        </div>--}}
        <div class="mrList"><span class="mrTitle">老顾客编号<i>*</i></span><input type="number" class="recommendNum" required><button  onclick="checkMember(1)">查询老顾客</button></div>
        <div class="mrList"><span class="mrTitle">新顾客编号<i>*</i></span><input type="number" class="contactNum"><button  onclick="checkMember(2)">查询新顾客</button></div>
        <div class="mrList"><span class="mrTitle">服务中心<i>*</i></span><input type="number" class="serviceCenter"><button onclick="checkMember(3)">查询服务中心</button></div>
        <div class="mrList"><span class="mrTitle">顾客区<i>*</i></span>
            <select name="" id="" class="LRArea">
                <option value="1">左</option>
                <option value="2">右</option>
            </select>
        </div>
        <div class="mrList"><span class="mrTitle">真实姓名<i>*</i></span><input type="text" class="realName"></div>
        <div class="mrList"><span class="mrTitle">性别<i>*</i></span>
            <select name="" id="" class="Sex">
                <option value="1">男</option>
                <option value="0">女</option>
            </select>
        </div>
        <div class="mrList"><span class="mrTitle">证件号码<i>*</i></span><input type="text" class="IDNum needBig"></div>
        <div class="mrList"><span class="mrTitle">手机号码<i>*</i></span><input type="number" class="phone "></div>
        <div class="mrList"><span class="mrTitle">手机验证码<i>*</i></span><input type="number" class="regNum"><button onclick="getPhoneReg()" id="phoneReg">获取手机验证码</button></div>
        <div class="mrList mrList1"><span class="mrTitle">地区</span>
            <select name="" id="pro" onchange="getCity()">
            </select>
            <select name="" id="city" onchange="getCountry()">
            </select>
            <select name="" id="country">
            </select>
        </div>
        <div class="mrList"><span class="mrTitle">详细地址<i>*</i></span><input type="text" class="address needBig"></div>
        <div class="mrList mrList1"><span class="mrTitle">开户行</span>
            <select name="" class="province" id="_bank" onchange="getBankPro()"></select>
            <select name="" id="_bankPro" onchange="getBankCity()"></select>
            <select name="" id="_bankCity" onchange="getBankCintry()"></select>
        </div>
        <div class="mrList"><span class="mrTitle">开户支行</span><select name="" id="_bankCountry"></select></div>
        <script>
            //获得城市三级联动
            var proArr = [],
                cityArr = [],
                countryArr =[];
            $.ajax({
                url: "js/area.json",
                type: "get",
                dataType: "json",
                success:function (e) {
                    proArr = e;
                    cityArr = e[0].city;
                    countryArr = e[0].city[0].area;
                    for (var i in proArr) {
                        $("#pro").append("<option>"+proArr[i].name+"</option>")
                    }
                    for (var j in cityArr) {
                        $("#city").append("<option>"+cityArr[j].name+"</option>")
                    }
                    for (var k in countryArr) {
                        $("#country").append("<option>"+countryArr[k]+"</option>")
                    }
                },
                error:function (e) {
                    console.log(e)
                }
            });
            /*
    *选择 省份 变化 城市
    *
    * */
            var proIndex,
                cityIndex;
            function getCity() {
                proIndex = $("#pro option:selected").index();
                $("#city").html("");
                $("#country").html("");
                cityArr = proArr[proIndex].city;
                countryArr = proArr[proIndex].city[0].area;
                for (var j in cityArr) {
                    $("#city").append("<option>"+cityArr[j].name+"</option>")
                };
                for (var k in countryArr) {
                    $("#country").append("<option>"+countryArr[k]+"</option>")
                };
            };
            /*
            *选择 省份 变化 县区
            *
            * */
            function getCountry() {
                cityIndex = $("#city option:selected").index();
                $("#country").html("");
                countryArr = proArr[proIndex].city[cityIndex].area;
                for (var k in countryArr) {
                    $("#country").append("<option>"+countryArr[k]+"</option>")
                };
            };

        </script>
        <div class="mrList"><span class="mrTitle">开户账号</span><input type="number" class="openBankCard needBig"></div>
        <div class="mrList"><span class="mrTitle">开户人</span><input type="text" class="openBankName"></div>
        <div class="mrList"><span class="mrTitle">验证码<i>*</i></span><input type="text" class="picReg"><img src="{{ captcha_src() }}" onclick="this.src='{{ captcha_src() }}?r='+Math.random();" alt=""></div>
        <button onclick="regNow()">立即注册</button>
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

        var z_tel = /^1[3|4|5|6|7|8|9]\d{9}$/,
            isClick = true ;
        /*
         *立即注册
         * */
        function regNow() {
            var _regAccount = $(".regAccount").val(), // 注册账号
                _level = $(".level option:selected").val(), // 注册客户类型
                _recommendNum = $(".recommendNum").val(), // 老顾客编号
                _contactNum = $(".contactNum").val(), // 新顾客编号
                _serviceCenter = $(".serviceCenter").val(), // 服务中心
                _LRArea = $(".LRArea option:selected").val(), // 安置区间
                _realName = $(".realName").val(), // 真实姓名
                _sex =$(".Sex option:selected").val(), // 性别
                _IDNum = $(".IDNum").val(), // 证件号码
                _phone = $(".phone").val(), // 手机号码
                _regNum = $(".regNum").val(), // 手机验证码
                _areaPro = $("#pro option:selected").html(), // 地区-省份
                _areaCity = $("#city option:selected").html(), // 地区-市区
                _areaCountry= $("#country option:selected").html(), // 地区-县城
                _address = $(".address").val(), // 地址
                _openBank = $("#_bank option:selected").html(), // 开户行
                _openBankAds = $("#_bankCountry option:selected").html(), // 开户支行
                _openBankAdsCode = $("#_bankCountry option:selected").attr("data-code"), // 开户支行代码
                _openBankCard = $(".openBankCard").val(), // 开户账号
                _openBankName = $(".openBankName").val(), // 开户人
                _picReg = $(".picReg").val(); // 图片验证码
            if (_recommendNum == '') {
                layer.msg('老顾客编号不能为空', {icon: 5});
                return false;
            };
            if (_contactNum == '') {
                layer.msg('新顾客编号不能为空', {icon: 5});
                return false;
            };
            if (_serviceCenter == '') {
                layer.msg('服务中心号码不能为空', {icon: 5});
                return false;
            };
            if (_realName == '') {
                layer.msg('真实姓名不能为空', {icon: 5});
                return false;
            };
            if (_IDNum == '') {
                layer.msg('身份证码不能为空', {icon: 5});
                return false;
            };
            if (_phone == '') {
                layer.msg('电话号码不能为空', {icon: 5});
                return false;
            };
            if (_regNum == '') {
                layer.msg('短信验证码不能为空', {icon: 5});
                return false;
            };
            if (_address == '') {
                layer.msg('地址不能为空', {icon: 5});
                return false;
            };
            if (_picReg == '') {
                layer.msg('图片验证码不能为空', {icon: 5});
                return false;
            };
            //ajax()请求
            $.ajax({
                url:"/register",
                type:"post",
                dataType:"json",
                data:{
                    account:_regAccount,  // 账号
                    level:1,
                    recommend_code:_recommendNum, // 老顾客编号
                    contact_code: _contactNum, // 新顾客编号
                    center_code: _serviceCenter, // 报单中心
                    place: _LRArea, // 安置区间
                    realname: _realName, // 真实姓名
                    sex: _sex, // 性别
                    id_number: _IDNum, // 身份证号码
                    phone: _phone, // 手机号
                    province: _areaPro, // 注册省份
                    city: _areaCity, // 注册市区
                    country: _areaCountry, // 注册县区
                    detail: _address, // 详细地址
                    bank_name: _openBank, //开户行
                    bank_code: _openBankAdsCode, // 开户支行代码
                    bank_code_name:_openBankAds, // 开户支行名称
                    bank_card: _openBankCard, // 开户账号
                    bank_account: _openBankName, //开户人
                    cpt: _picReg // 验证码
                },
                success:function (e) {
                    layer.msg(e.msg);
                },
                error:function (e) {
                    console.log(e)
                }
            })
        }
        /*
        * 查询会员是否存在
        * */
        function checkMember(idx) {
            var _recommendNum = $(".recommendNum").val(),
                _contactNum = $(".contactNum").val(),
                _serviceCenter = $(".serviceCenter").val();
            switch (idx){
                case 1:
                    $.ajax({
                        url:"/checkUser",
                        type:"post",
                        dataType:"json",
                        data:{code:_recommendNum,type:'recommend'},
                        success:function (e) {
                            layer.msg(e.msg);
                        },
                        error:function (e) {
                            console.log(e)
                        }
                    });
                    break;
                case 2:
                    $.ajax({
                        url:"/checkUser",
                        type:"post",
                        dataType:"json",
                        data:{code:_contactNum,type:'contact'},
                        success:function (e) {
                            layer.msg(e.msg);
                        },
                        error:function (e) {
                            console.log(e)
                        }
                    });
                    break;
                case 3:
                    $.ajax({
                        url:"/checkUser",
                        type:"post",
                        dataType:"json",
                        data:{code:_serviceCenter,type:'service'},
                        success:function (e) {
                            layer.msg(e.msg);
                        },
                        error:function (e) {
                            console.log(e)
                        }
                    });
                    break;
                default:
                    return false;
            }
        };
        /*
        * 获取短信验证码
        * */
        function getPhoneReg() {
            var _phone = $(".phone").val();
            if (_phone == ''){
                layer.msg('手机号码不能为空', {icon: 5});
                return false;
            }else if (z_tel.test(_phone) == false) {
                layer.msg('手机号码格式错误', {icon: 5});
                return false;
            }else {
                if (isClick) {
                    isClick = false;
                    var num = 120,
                        time=setInterval(function () {
                            num--;
                            if (num == 0){
                                clearInterval(time);
                                $("#phoneReg").html("获取验证码").css({
                                    color:"#51c2d4",
                                    borderColor:"#51c2d4"
                                });;
                                isClick = true;
                            }else {
                                $("#phoneReg").html(num+ "s后重发").css({
                                    color:"#ccc",
                                    borderColor:"#ccc"
                                });
                            }
                        },1000);
                    // 发送短信请求
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
                }
            }
        };


        var _bank,
            _bankName,
            _bankPro,
            _bankProName,
            _bankCity,
            _bankCityName,
            _bankCountry;
        /*
       * 获取开户支行
       * */
        $.ajax({
            url:"/banksList",
            type:"post",
            dataType:"json",
            data:{},
            success:function (e) {
                _bank = e.data;
                for (var i in _bank){
                    $("#_bank").append("<option>"+_bank[i].bank_name+"</option>")
                };
                $("#_bankPro").html("");
                $("#_bankCity").html("");
                $("#_bankCountry").html("")
            },
            error:function (e) {
                console.log(e)
            }
        });
        /*
        * 获取开户行省份
        * */
        function getBankPro() {
            _bankName = $("#_bank option:selected").html();
            $("#_bankPro").html("");
            $("#_bankCity").html("");
            $("#_bankCountry").html("");
            $.ajax({
                url:"/bankProvince",
                type:"post",
                dataType:"json",
                data:{bank:_bankName},
                success:function (e) {
                    _bankPro = e.data;
                    $("#_bankPro").html("");
                    for (var i in _bankPro) {
                       $("#_bankPro").append("<option>"+_bankPro[i].province+"</option>")
                    }
                },
                error:function (e) {
                    console.log(e)
                }
            })
        };
        /*
        * 获取开户行城市
        * */
        function getBankCity() {
            _bankProName = $("#_bankPro option:selected").html();
            $.ajax({
                url:"/bankCity",
                type:"post",
                dataType:"json",
                data:{
                    bank:_bankName,
                    province:_bankProName
                },
                success:function (e) {
                    _bankCity = e.data;
                    $("#_bankCity").html("");
                    for (var i in _bankCity) {
                        $("#_bankCity").append("<option>"+_bankCity[i].area+"</option>")
                    }

                },
                error:function (e) {
                    console.log(e)
                }
            });
        };
        /*
        *获取开户行支行
        * */
        function getBankCintry() {
            _bankCityName = $("#_bankCity option:selected").html();
            $.ajax({
                url:"/bankCodeList",
                type:"post",
                dataType:"json",
                data:{
                    bank:_bankName,
                    province:_bankProName,
                    area:_bankCityName
                },
                success:function (e) {
                    _bankCountry = e.data;
                    $("#_bankCountry").html("");
                    for (var i in _bankCountry) {
                        $("#_bankCountry").append("<option data-code='"+_bankCountry[i].code+"'>"+_bankCountry[i].name+"</option>")
                    };
                }
            });
        };
    </script>
@endsection
@section('script')
    @parent
    <script src="{{URL::asset('js/area.json')}}"></script>
@show