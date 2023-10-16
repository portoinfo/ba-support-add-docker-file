@extends('layouts2.app')

@section('title')
Edit Company
@endsection

@section('content')
    <!-- create a company from the home dashboard -->
    <company-edit-controller
        :usuario="{{ $usuario }}"
        is_helpdesk="{{ $is_helpdesk }}"
        :csid="`{{ $csid }}`"
        :viewcontact="{{ $viewcontact }}"
        :save-href="`{{ $save_href }}`"
        :cancel-href="`{{ $cancel_href }}`"
        :update-href="`{{ $update_href }}`"
        :base_url="`{{  App::make('url')->to('/') }}`"
    ></company-edit-controller>
@endsection