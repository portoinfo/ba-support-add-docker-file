@extends('layouts2.app')

@section('title')
User Client
@endsection

@section('content')
	<div id="app">
		<user-client-body
			:user="{{ Auth::user()->toJson() }}"
			:base_url="`{{ url('/') }}`"
			:go_back_url="`{{ url('/user-client') }}`"
		></user-client-body>	
	</div>
@endsection