@extends('layouts2.app')

@section('title')
Register Departments
@endsection

@section('content')	
	<create-department-controller
		:base_url="`{{ $base_url }}`"
		:save_redirect_url="`{{ $save_redirect_url }}`"
		:go_back_redirect_url="`{{ $go_back_redirect_url }}`"
	></create-department-controller>
@endsection