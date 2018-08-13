@extends('layouts.app')
@section('title', '银行卡列表')
@section('content')
    <table>
        <tr>
            <th>id</th>
            <th>户名</th>
            <th>开户行</th>
            <th>卡号</th>
            <th>创建时间</th>
        </tr>
        @foreach ($data as $list)
            <tr>
                <td>{{ $list->id }}</td>
                <td>{{ $list->account }}</td>
                <td>{{ $list->bank }}</td>
                <td>{{ $list->card }}</td>
                <td>{{ $list->created_at }}</td>
            </tr>
        @endforeach
    </table>
@endsection