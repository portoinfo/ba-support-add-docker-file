@extends('layouts2.app', ['fullpage' => true])

@section('title')
Login Client
@endsection

@section('content')
	<login-ba-suporte-c 
		:company="{{json_encode($company)}}"
		:acesso_anonymous="{{ json_encode($acesso_anonymous) }}"
		:settings="{{ json_encode($settings) }}"
	></login-ba-suporte-c>
@endsection
