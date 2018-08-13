@extends('layouts.app')
@section('title', '客户系统')
@section('content')
<!-- 顶部开始 -->
<div class="container">
    <div class="logo"><a href="/index"></a></div>
    <div class="left_open">
        <i title="展开左侧栏" class="iconfont">&#xe699;</i>
    </div>
    <ul class="layui-nav right" >
        <li class="layui-nav-item">
            <div class="index-user-info">
                <img src="{{URL::asset('web/images/head.png')}}" class="left"/>
                <p class="left user_name">{{ $data->account }}<span>(代理商)</span></p>
            </div>
        </li>
        <li class="layui-nav-item"><p class="news"><i class="iconfont">&#xe6bc;</i>消息</p></li>
        <li class="layui-nav-item">

            <p class="quit-login" style="cursor: pointer"><i class="iconfont">&#xe64b;</i>退出</p>
        </li>
    </ul>
    <script>
        $('.quit-login').click(
            function(){
                location.href = '/loginOut';
            }
        );
    </script>

</div>
<!-- 顶部结束 -->
<!-- 中部开始 -->
<!-- 左侧菜单开始 -->
<div class="left-nav">
    <div id="side-nav">
        <ul id="nav">
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe6b8;</i>
                    <cite>基本信息</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="/resetPassword">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>修改登录密码</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/resetPayment">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>修改支付密码</cite>
                        </a>
                    </li>

                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe6f5;</i>
                    <cite>会员注册</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="/register">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>会员注册</cite>
                        </a>
                    </li >
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe68c;</i>
                    <cite>客户信息</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="/myRecommend">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>客户</cite>
                        </a>
                    </li >
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe66b;</i>
                    <cite>个人积分</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="assets">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>账户积分</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/transformAddedPoints">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>礼品积分转换</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/transformElectronic">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>增值积分转换</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/shopPointsTransfer">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>购物积分转账</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/electronicTransfer">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>积分转账</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/applyWithdraw">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>申请转账</cite>
                        </a>
                    </li >
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe61b;</i>
                    <cite>个人记录</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="/transformAddedPointsList">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>礼品积分转换记录</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/transformElectronicList">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>增值积分转换记录</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/shopPointsLog">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>购物积分收支记录</cite>
                        </a>
                    </li >

                    <li>
                        <a _href="/dynamicLog">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>增值积分奖励记录</cite>
                        </a>
                    </li >
                    <li>
                        <a _href="/staticLog">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>礼品积分奖励记录</cite>
                        </a>
                    </li>
                    <li>
                        <a _href="/transferList">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>积分转账记录</cite>
                        </a>
                    </li>
                    <li>
                        <a _href="/withdrawList">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>转账记录</cite>
                        </a>
                    </li >

                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe616;</i>
                    <cite>购物中心</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="/mall/shopCenter">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>购物中心</cite>
                        </a>
                    </li >
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe613;</i>
                    <cite>服务管理</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">

                    @if($data->level == '代理商')
                    {{--<li>--}}
                        {{--<a _href="/apply">--}}
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            {{--<cite>申请为服务中心</cite>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    @endif
                    @if($data->level == '服务中心')
                    <li>
                        <a _href="/waitCheck">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>待审核列表</cite>
                        </a>
                    </li>
                    <li>
                        <a _href="/checkLog">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>审核记录</cite>
                        </a>
                    </li>
                    @endif
                  {{--  @if($data->level == '服务中心')
                        <li>
                            <a _href="/centerRecommend">
                                --}}{{--<i class="iconfont">&#xe6a7;</i>--}}{{--
                                <cite>我的推荐</cite>
                            </a>
                        </li >
                    @endif--}}
                        <li>
                            <a _href="/feedback">
                                {{--<i class="iconfont">&#xe6a7;</i>--}}
                                <cite>信息反馈</cite>
                            </a>
                        </li >
                </ul>
            </li>
            <li>
                <a href="javascript:;">
                    <i class="iconfont">&#xe644;</i>
                    <cite>公司信息</cite>
                    <i class="iconfont nav_right">&#xe6a7;</i>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a _href="/aboutus">
                            {{--<i class="iconfont">&#xe6a7;</i>--}}
                            <cite>公司信息</cite>
                        </a>
                    </li >
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- <div class="x-slide_left"></div> -->
<!-- 左侧菜单结束 -->
<!-- 右侧主体开始 -->
<div class="page-content">
    <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
        <ul class="layui-tab-title">
            <li class="home"><i class="layui-icon">&#xe68e;</i>我的桌面</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <iframe src='/welcome' frameborder="0" scrolling="yes" class="x-iframe"></iframe>
            </div>
        </div>
    </div>
</div>
<div class="page-content-bg"></div>
<!-- 右侧主体结束 -->
<!-- 中部结束 -->
<!-- 底部开始 -->
{{--<div class="footer">
    <div class="copyright">Copyright ©2017 x-admin v2.3 All Rights Reserved</div>
</div>--}}
<!-- 底部结束 -->
@endsection