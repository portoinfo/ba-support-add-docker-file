<?php
    $gravatar = App\Tools\Gravatar::getGravatar(Auth::user()->email);
?>

@section('title')
Home
@endsection

@extends('layouts2.app', ['fullpage' => true])

@section('content')
	@if(session('is_admin') == 1)
	 	<the-navbar csid="{{App\Tools\Crypt::decrypt(session('companyselected.id'))}}"  :session_user_company="{{ json_encode(session('companyselected')) }}" :usuario="{{ Auth::user()->toJson() }}" csname="{{session('companyselected.name')}}" cslogo="{{session('companyselected.logo')}}" :gravatar="{{ json_encode($gravatar)}}" base_url="{{ App::make('url')->to('/') }}"></the-navbar>
        <div class="container-fixed">
            <the-sidebar :session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}" :user="{{ Auth::user()->toJson() }}" :restriction="{{json_encode(session('restriction'))}}" is_admin="{{session('is_admin')}}" current="{{ Route::currentRouteName() }}" base_url="{{ App::make('url')->to('/') }}" :company="`{{ json_encode(session('companyselected')) }}`" :user_departments_id="`{{ json_encode(session('user_departments_id')) }}`" ></the-sidebar>
            <main id="main" class="mini">
            <b-container>
                <div class="flex-fill px-sm-0 py-3">
                   <home-admin-dashboard :usuario="{{ Auth::user()->toJson() }}" csname="{{session('companyselected.name')}}" csid="{{ session('companyselected.id') }}" cslogo="{{session('companyselected.logo')}}" :gravatar="{{ json_encode($gravatar)}}" :base_url="`{{ url('/') }}`"></home-admin-dashboard>
                </div>
            </b-container>
            </main>
        </div>
	@else
        <the-navbar csid="{{App\Tools\Crypt::decrypt(session('companyselected.id'))}}"  :session_user_company="{{ json_encode(session('companyselected')) }}" :usuario="{{ Auth::user()->toJson() }}" csname="{{session('companyselected.name')}}" cslogo="{{session('companyselected.logo')}}" :gravatar="{{ json_encode($gravatar)}}" base_url="{{ App::make('url')->to('/') }}"></the-navbar>
        <div class="container-fixed">
            <the-sidebar :session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}" :user="{{ Auth::user()->toJson() }}" :restriction="{{json_encode(session('restriction'))}}" is_admin="{{session('is_admin')}}" current="{{ Route::currentRouteName() }}" base_url="{{ App::make('url')->to('/') }}" :company="`{{ json_encode(session('companyselected')) }}`" :user_departments_id="`{{ json_encode(session('user_departments_id')) }}`" ></the-sidebar>
            <main id="main" class="mini">
            <b-container>
                <div class="flex-fill px-sm-0 py-3">
                    <home-employee-dashboard :attendant_id="`{{ $attendant_id }}`" :usuario="{{ Auth::user()->toJson() }}" csname="{{session('companyselected.name')}}" csid="{{ session('companyselected.id') }}" cslogo="{{session('companyselected.logo')}}" :gravatar="{{ json_encode($gravatar)}}" :base_url="`{{ url('/') }}`"></home-employee-dashboard>
                </div>
            </b-container>
            <tab-global  :session_user_permissions="{{ json_encode(session('restriction')) }}"  :restriction="{{json_encode(session('restriction'))}}" company_selected="{{ json_encode(session('companyselected')) }}" :session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"  :user="{{ Auth::user()->toJson() }}"></tab-global>
            </main>
        </div>
	@endif
@endsection
