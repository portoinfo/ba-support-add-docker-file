@extends('layouts2.app')

@section('title')
Dashboard Departments
@endsection

@section('content')	
	<department-dashboard
		:base_url="`{{ url('/') }}`"
		:usuario="{{ Auth::user()->toJson() }}"
		:timezones="{{ json_encode(\App\Tools\Timezone::all) }}"
		:csid="`{{session('companyselected.id')}}`"
	></department-dashboard>
@endsection