    <?php
        use App\Tools\Client;
        $logout = Client::getLogoutData();
        $dtype =  json_encode(session('dtype'));
    ?>
    @extends('layouts2.app2', ['fullpage' => true])
    @section('content')

    <navbar-client 
        session_dtype="{{ $dtype }}" 
        :user="{{ Auth::user()->toJson() }}" 
        csname="{{session('companyselected.name')}}" 
        cslogo="{{session('companyselected.logo')}}" 
        show_logout="{{ json_encode($logout['show_logout']) }}"
        user_attendant="{{ session('user_attendant') != null }}">
    </navbar-client>
    <div class="container-fixed w-100 h-100 m-0">
        <sidebar-client style="margin-top: 60px;"
        current="{{ Request::path() }}"
        :user="{{ Auth::user()->toJson() }}"
        csucid="{{session('companyselected.user_client_id')}}"
        csid="{{session('companyselected.company_id')}}"
        csid2="{{App\Tools\Crypt::decrypt(session('companyselected.id'))}}"
        csname="{{session('companyselected.name')}}"
        cslogo="{{session('companyselected.logo')}}"
        show_logout="{{ json_encode($logout['show_logout']) }}"
        :restriction_client="{{json_encode(session('restriction_client'))}}"
        session_dtype="{{ $dtype }}"
        >
        </sidebar-client>
        <main id="main" class="mini h-100">
            <b-container fluid class="pt-2 h-100">
                <chat-client
                    :user="{{ Auth::user()->toJson() }}"
                    user_client_id="{{session('companyselected.user_client_id')}}"
                    user_auth_id="{{session('companyselected.user_auth_id')}}"
                    setting_chat="{{session('companyselected.settings_chat')}}"
                    company_id="{{session('companyselected.company_id')}}"
                    session_dtype="{{ $dtype }}"
                    session_show_only_dtype="{{ session('show_only_dtype') }}"
                    session_cscode="{{ session('cscode') }}"
                    session_plan="{{ session('plan') }}"
                    session_fee="{{ session('fee') }}"
                    session_department_id="{{ session('department_id') }}">
                </chat-client>
            </b-container>
        </main>
    </div>
    @endsection
