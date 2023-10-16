@extends('layouts2.app')

@section('title')
Integration Company
@endsection

@section('content')
	<company-integration
		:usuario="{{ Auth::user()->toJson() }}"
		:hash_code="`{{session('companyselected.hash_code')}}`"
		:csid="`{{session('companyselected.id')}}`"
		:base_url="`{{  App::make('url')->to('/') }}`"
	></company-integration>
@endsection