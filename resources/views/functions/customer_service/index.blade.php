@extends('layouts2.app3')

@section('title')
    Customer Service
@endsection

@section('content')
    <customer-service
        :user="{{Auth::user()->toJson()}}"
        :session_user="{{ Auth::user()->toJson() }}"
        :session_user_departments="{{ json_encode(session('user_departments_id')) }}"
        :user_departments_id="{{ json_encode(session('user_departments_id')) }}"
        :session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"
        :session_user_permissions="{{ json_encode(session('restriction')) }}"
        :session_user_company="{{ json_encode(session('companyselected')) }}"
        :cs="{{json_encode(session('companyselected'))}}"
        :restriction="{{json_encode(session('restriction'))}}"
        :is_admin="{{session('is_admin')}}"
    ></customer-service>
@endsection
