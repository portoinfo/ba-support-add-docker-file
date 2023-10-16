@extends('layouts2.app2')

<?php
    $restriction = session('restriction')[0];
    $all_list = $restriction->chat_alllist;
    $chat_admin = $restriction->chat_admin;
?>

@section('title')
    @if ($all_list || $chat_admin)
        Full Chat
    @else
        Chat
    @endif
@endsection

@section('content')

<full-chat
	:session_user="{{ Auth::user()->toJson() }}"
	:session_user_departments="{{ json_encode(session('user_departments_id')) }}"
	:session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"
	:session_user_permissions="{{ json_encode(session('restriction')) }}"
	:session_user_company="{{ json_encode(session('companyselected')) }}"
    :restriction="{{json_encode(session('restriction'))}}"
    :is_admin="{{session('is_admin')}}">
</full-chat>

@endsection
