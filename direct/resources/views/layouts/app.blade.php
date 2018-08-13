<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{URL::asset('web/css/font.css')}}">
    <link rel="stylesheet" href="{{URL::asset('web/css/xadmin.css')}}">
    @section('style')
    @show
    <script src="{{URL::asset('js/jquery.min.js')}}"></script>
    <script src="{{URL::asset('web/lib/layui/layui.js')}}"></script>
    <script src="{{URL::asset('web/js/xadmin.js')}}"></script>
    @section('script')
        <script>
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        </script>
    @show
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>