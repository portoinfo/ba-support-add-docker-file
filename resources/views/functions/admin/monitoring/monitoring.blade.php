@extends('layouts2.app')

@section('title')
Dashboard Monitoring
@endsection

@section('content')
	<monitoring-body
        :session_user_company="{{ json_encode(session('companyselected')) }}"
        :session_user="{{ Auth::user()->toJson() }}"
	></monitoring-body>
    <tab-global style="display: none" :session_user_permissions="{{ json_encode(session('restriction')) }}"  :restriction="{{json_encode(session('restriction'))}}" company_selected="{{ json_encode(session('companyselected')) }}" :session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"  :user="{{ Auth::user()->toJson() }}"></tab-global>
@endsection
