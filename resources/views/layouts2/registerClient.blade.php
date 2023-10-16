@extends('layouts2.app', ['fullpage' => true])

@section('title')
Register Client
@endsection

@section('content')
	<register-ba-suporte-c 
		:company="{{json_encode($company)}}"
		:acesso_anonymous="{{ json_encode($acesso_anonymous) }}"
	></register-ba-suporte-c>
@endsection
