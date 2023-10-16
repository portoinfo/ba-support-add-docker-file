@extends('client.template.app', ['fullpage' => true])

@section('title')
Register Simple
@endsection

@section('content')
	<create-fast-ticket :depart_id="{{ json_encode($depart_id) }}" :depart_name="{{ json_encode($depart_name) }}"></create-fast-ticket>
@endsection
