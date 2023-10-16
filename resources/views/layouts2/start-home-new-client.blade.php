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
	>
	</admin-all> -->

	<admin-temporary class="vue" 
		csid="{{ session('companyselected.id') }}"
        type="client"
	>
	</admin-temporary>
	@if(session('is_admin') == 1)
	 	
	@else
        
	@endif
@endsection
