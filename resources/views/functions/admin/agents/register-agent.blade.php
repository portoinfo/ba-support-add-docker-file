@extends('layouts2.app')

@section('title')
Register Agents
@endsection

@section('content')
    <create-agent-controller
        :csid="`{{session('companyselected.id')}}`"
        :base_url="`{{ url('/') }}`"
        :go_back_url="`{{ url('/agents') }}`"
        :save_url="`{{ url('/agents/list-agents') }}`"
    ></create-agent-controller>
@endsection