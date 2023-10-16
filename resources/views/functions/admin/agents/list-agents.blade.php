@extends('layouts2.app')

@section('title')
List Agents
@endsection

@section('content')
	<agents-body
        :usuario="{{ Auth::user()->toJson() }}"
        :csid="`{{session('companyselected.id')}}`"
        :base_url="`{{ url('/') }}`"
        :go_back_url="`{{ url('/agents') }}`"
        :is_admin="`{{session('is_admin')}}`"
    ></agents-body>
@endsection