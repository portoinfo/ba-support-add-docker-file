@extends('layouts2.app2')

@section('title')
Tickets
@endsection

@section('content')
	<tickets-body
	:user="{{Auth::user()->toJson()}}" 
	:user_departments_id="{{ json_encode(session('user_departments_id')) }}" 
	:session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}" 
	:cs="{{json_encode(session('companyselected'))}}"
	:restriction="{{json_encode(session('restriction'))}}"
	:is_admin="{{session('is_admin')}}"
	></tickets-body>
@endsection