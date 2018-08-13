@extends('layouts.app')
@section('title', '关于我们')
@section('style')
    @parent
    <style type="text/css">

    </style>
@endsection
@section('content')
    <p>{{ $data }}</p>
@endsection