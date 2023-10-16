@extends('client.template.app')
@section('content')
	<client-login
        :company="{{json_encode($company)}}"
        :acesso_anonymous="{{ json_encode($acesso_anonymous) }}"
        :settings="{{ json_encode($settings) }}"
    ></client-login>
@endsection
