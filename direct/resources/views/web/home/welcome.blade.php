@extends('layouts.app')
@section('title', 'Home')
@section('style')
    @parent
    <style type="text/css">
        .welcome-center{
            margin-top: 160px;
            display: flex;
            align-items: center;
            justify-items: center;
        }
        .welcome-center >div{
            display: inline-block;
            margin: 0 auto;
        }
        .welcome-center h1{

        }
    </style>
@endsection
@section('content')
    <div class="welcome-center">
        <div>
            <h1 class="c_word">欢迎来到恒康堂会员管理系统</h1>
            <p class="c_mete">有远大抱负的人不可忽略眼前的工作</p>
            <p class="c_articer">—— 欧里庇得斯</p>
        </div>
    </div>
@endsection