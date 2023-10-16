@extends('layouts2.app', ['fullpage' => true])

@section('title')
Login Telegram
@endsection

@section('content')
        <login-ba-telegram
            getuser="{{json_encode($getuser)}}"
            token="{{$token}}"
            company_user="{{$company_user}}"
        ></login-ba-telegram>
@endsection
