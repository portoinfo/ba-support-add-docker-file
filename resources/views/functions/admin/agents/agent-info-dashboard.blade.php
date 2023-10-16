@extends('layouts2.app')

@section('title')
Info Agents
@endsection

@section('content')
	<agent-info-dashboard
		:base_url="`{{ url('/') }}`"
        :go_back_url="`{{ url('/agents/list-agents') }}`"
		:agent="{{ $agent }}"
		:usuario="{{ Auth::user()->toJson() }}"
		:csid="`{{session('companyselected.id')}}`"
		:timezones="{{ json_encode(\App\Tools\Timezone::all) }}"
	></agent-info-dashboard>
@endsection