@extends('layouts2.app')

@section('title')
List Departments
@endsection

@section('content')	
	<department-body
		:usuario="{{ $usuario }}"
		:timezones="{{ $timezones }}"
		:base_url="`{{ $base_url }}`"
		:go_back_url="`{{ $go_back_url }}`"
		:company_id="`{{ session('companyselected')['id'] }}`"
	></department-body>
@endsection