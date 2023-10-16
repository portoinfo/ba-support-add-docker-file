@extends('layouts2.app2')

@section('title')
Full Ticket
@endsection

@section('content')

<full-ticket 
	:user="{{Auth::user()->toJson()}}" 
	:user_departments_id="{{ json_encode(session('user_departments_id')) }}" 
	:cs="{{json_encode(session('companyselected'))}}"
	:restriction="{{json_encode(session('restriction'))}}"
	:is_admin="{{session('is_admin')}}">
</full-ticket>

@endsection