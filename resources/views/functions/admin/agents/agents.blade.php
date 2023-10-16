@extends('layouts2.app')

@section('title')
Dashboard Agents
@endsection

@section('content')
	<agents-dashboard
		:usuario="{{ Auth::user()->toJson() }}" csid="{{session('companyselected.id')}}"
		:list_agent_url="`{{ url('/agents/list-agents') }}`"
		:create_agent_url="`{{ url('/agents/list-agents?new=agent') }}`"
		:base_url="`{{ url('/') }}`"
		:usuario="{{ Auth::user()->toJson() }}"
		:timezones="{{ json_encode(\App\Tools\Timezone::all) }}"
		:csid="`{{session('companyselected.id')}}`"
	></agents-dashboard>
@endsection