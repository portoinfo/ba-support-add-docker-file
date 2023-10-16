@extends('layouts2.app')

@section('title')
Dashboard Company
@endsection

@section('content')
	<company-dashboard
		:usuario="{{ Auth::user()->toJson() }}"
		:csid="`{{session('companyselected.id')}}`"
		:csname="`{{session('companyselected.name')}}`"
		:base_url="`{{  App::make('url')->to('/') }}`"
		:create-href="`{{  App::make('url')->to('/register-new-company') }}`"
        :edit-href="`{{  App::make('url')->to('/edit-company') }}`"
		:is_admin="`{{session('is_admin')}}`"
	></company-dashboard>
@endsection