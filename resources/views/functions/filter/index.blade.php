@extends('layouts2.app2')

@section('title')
Filter
@endsection

@section('content')

<filter-tickets-chats
	:session_user="{{ Auth::user()->toJson() }}"
	:session_user_departments="{{ json_encode(session('user_departments_id')) }}"
	:session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"
	:session_user_permissions="{{ json_encode(session('restriction')) }}"
	:session_user_company="{{ json_encode(session('companyselected')) }}"
/>

@endsection
