<?php
    use App\Tools\Client;
    $logout = Client::getLogoutData();
    $gravatar = App\Tools\Gravatar::getGravatar(Auth::user()->email);
    $dtype =  json_encode(session('dtype'));
?>
@section('title')
Home
@endsection
@extends('layouts2.app2', ['fullpage' => true])
@section('content')
<navbar-client 
    session_dtype="{{ $dtype }}" 
    :gravatar="{{ json_encode($gravatar)}}"  
    :user="{{ Auth::user()->toJson() }}" 
    csname="{{session('companyselected.name')}}" 
    cslogo="{{session('companyselected.logo')}}" 
    show_logout="{{ json_encode($logout['show_logout']) }}"
    user_attendant="{{ session('user_attendant') != null }}">
</navbar-client>
<div class="container-fixed w-100 h-100 m-0">
    <sidebar-client style="margin-top: 60px;"
    current="{{ Request::path() }}"
    :user="{{ Auth::user()->toJson() }}"
    csucid="{{session('companyselected.user_client_id')}}"
    csid="{{session('companyselected.company_id')}}"
    csid2="{{App\Tools\Crypt::decrypt(session('companyselected.id'))}}"
    csname="{{session('companyselected.name')}}"
    cslogo="{{session('companyselected.logo')}}"
    show_logout="{{ json_encode($logout['show_logout']) }}"
    :restriction_client="{{json_encode(session('restriction_client'))}}"
    session_dtype="{{ $dtype }}">
    </sidebar-client>

    <main id="main" class="mini h-100">
        <b-container fluid class="pt-2">
            <home-client
            :usuario="{{ Auth::user()->toJson() }}"
            :restriction_client="{{json_encode(session('restriction_client'))}}">
            </home-client>
        </b-container>
    </main>
</div>
@endsection
