@extends('layouts2.app')

@section('title')
Dashboard Analyze
@endsection

@section('content')
	<analyze-body
		{{-- :usuario="{{ Auth::user()->toJson() }}" 
		csid="{{session('companyselected.id')}}"
		:list_agent_url="`{{ url('/agents/list-agents') }}`"
		:create_agent_url="`{{ url('/agents/list-agents?new=agent') }}`"
		:base_url="`{{ url('/') }}`"
		:timezones="{{ json_encode(\App\Tools\Timezone::all) }}"
		:csid="`{{session('companyselected.id')}}`" --}}
		:session_user="{{ Auth::user()->toJson() }}"
		:timezones="{{ json_encode(\App\Tools\Timezone::all) }}"
	></analyze-body>
@endsection