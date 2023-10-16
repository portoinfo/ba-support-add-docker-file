<?php
    use App\Tools\Client;
    $logout = Client::getLogoutData();
    $gravatar = App\Tools\Gravatar::getGravatar(Auth::user()->email);
    $dtype =  json_encode(session('dtype'));
?>
@extends('client.template.app')
@section('content')
    <client-app
        base_url="{{ App::make('url')->to('/') }}"
        :user="{{ Auth::user()->toJson() }}"
        hc="{{session('companyselected.company_id')}}"
        csname="{{session('companyselected.name')}}"
        csid="{{App\Tools\Crypt::decrypt(session('companyselected.id'))}}"
        user_client_id="{{session('companyselected.user_client_id')}}"
        setting_chat="{{session('companyselected.settings_chat')}}"
        :restriction_client="{{json_encode(session('restriction_client'))}}"
        session_dtype="{{ $dtype }}"
        session_show_only_dtype="{{ session('show_only_dtype') }}"
        session_cscode="{{ session('cscode') }}"
        session_plan="{{ session('plan') }}"
        session_fee="{{ session('fee') }}"
        session_department_id="{{ session('department_id') }}"
        is_popup="{{ session('isPopup') }}"
    >
    </client-app>
@endsection
