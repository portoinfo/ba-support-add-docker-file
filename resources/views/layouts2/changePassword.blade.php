@extends('layouts2.app', ['fullpage' => true])

@section('title')
Change Password
@endsection

@section('content')
	<change-password 
		:result="{{json_encode($result)}}"></change-password>
@endsection
