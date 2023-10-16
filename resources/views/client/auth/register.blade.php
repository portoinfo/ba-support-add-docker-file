@extends('client.template.app')
@section('content')
	<client-register
        :company="{{json_encode($company)}}"
        :acesso_anonymous="{{ json_encode($acesso_anonymous) }}"
    ></client-register>
@endsection
