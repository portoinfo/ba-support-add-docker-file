@extends('layouts2.app')

@section('title')
Group
@endsection

@section('content')
	<div id="app">
		<group-body
			:usuario="{{ Auth::user()->toJson() }}"
			:base_url="`{{ url('/') }}`"
			:go_back_url="`{{ url('/group') }}`"
		></group-body>	
	</div>
@endsection