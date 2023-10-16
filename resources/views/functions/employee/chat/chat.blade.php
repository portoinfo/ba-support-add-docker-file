@extends('layouts2.app2')

@section('title')
Chats
@endsection

@section('content')
	{{-- <chat-body
	:user="{{ Auth::user()->toJson() }}"
	user_departments_id="{{ json_encode(session('user_departments_id')) }}"
	restriction="{{ json_encode(session('restriction')) }}"
	:session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"
	company_selected="{{ json_encode(session('companyselected')) }}">
	</chat-body> --}}
    <chat-body></chat-body>
@endsection
