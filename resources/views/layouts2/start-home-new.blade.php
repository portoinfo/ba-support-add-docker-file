<?php
	use Illuminate\Support\Facades\Auth;

    $gravatar = App\Tools\Gravatar::getGravatar(Auth::user()->email);
?>

@section('title')
Home
@endsection

@extends('layouts2.app4', ['fullpage' => true])

@section('content')
	<!-- <admin-all class="vue" 
		csid="{{ session('companyselected.id') }}"
		:session_user="{{ Auth::user()->toJson() }}"
		:session_user_departments="{{ json_encode(session('user_departments_id')) }}"
		:session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"
		:session_user_permissions="{{ json_encode(session('restriction')) }}"
		:session_user_company="{{ json_encode(session('companyselected')) }}"
		:restriction="{{json_encode(session('restriction'))}}"
		:is_admin="{{session('is_admin')}}"
	>
	</admin-all> -->
	<!-- style="height: 91.7%;" -->
	<admin-temporary class="vue"
		csid="{{ session('companyselected.id') }}"
		type="admin"
	>
	</admin-temporary>
	@if(session('is_admin') == 1)
	 	
	@else
        
	@endif
@endsection
