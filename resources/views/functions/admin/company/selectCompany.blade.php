@extends('layouts2.app')

@section('title')
Select Company
@endsection

@section('content')
	<company-select :usuario="{{ Auth::user()->toJson() }}" is_admin="{{session('is_admin')}}" :base_url="`{{  App::make('url')->to('/') }}`" >
	</company-select>
@endsection

