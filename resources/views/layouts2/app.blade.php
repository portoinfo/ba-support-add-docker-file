<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="google" content="notranslate" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#FFFFFF">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#FFFFFF">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#FFFFFF">
    <link rel="apple-touch-icon" href="{{ asset('images/bs_apple_icons/bs_1024.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('images/bs_apple_icons/bs_120.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/bs_apple_icons/bs_152.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('images/bs_apple_icons/bs_167.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/bs_apple_icons/bs_180.png') }}">

    <!-- Google Analytics -->
    @extends('plugins.google-analytics')
    @if(Request::segment(1) == 'login' || Request::segment(1) == 'register' ||
        Request::segment(3) == 'login' || Request::segment(3) == 'register' ||
        Request::segment(1) == 'blocked' || Request::segment(1) == 'change-password')
        <meta name="user-lang" content="en_US">
        @elseif(Request::segment(1) == 'client' || Request::segment(1) == 'client-chat' || Request::segment(1) == 'client-ticket')
        <meta name="user-lang" content="{{ session('user')->language }}">
    @else
        <meta name="user-lang" content="{{ Auth::user()->language }}">
    @endif
    <title>@yield('title') | Builderall - Support</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}" />
    <!-- <link href="{{ asset('css/app.css?version='.config('app.version')) }}" rel="stylesheet"> -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css?version='.config('app.version')) }}" rel="stylesheet">
    <link href="{{ asset('css/robot.css?version='.config('app.version')) }}" rel="stylesheet">
    <link href="{{ asset('css/css-google.css?version='.config('app.version')) }}" rel="stylesheet">
    <link href="{{ asset('css/css2-google.css?version='.config('app.version')) }}" rel="stylesheet">
    <link href="{{ asset('css/css3-google.css?version='.config('app.version')) }}" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link rel="shortcut icon" href="{{ asset('images/chat.png') }}"> -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <script src="{{ asset('js/moment.min.js') }}"></script>
    {{-- <script src="{{ asset('js/moment-with-locales.min.js') }}"></script> --}}
    <script src="{{ asset('js/sweetalert2@10.js') }}"></script>
    <script src="{{ asset('js/sorttable.js') }}"></script>
</head>
<body>
    @if ($fullpage ?? '')
    <div id="app" class="app1">
        <meta name="user-lang" content="en_US">
        @yield('content')
    </div>
    @else
    <div id="app" class="app1">
        <?php
            $user2 = Auth::user()->language;
            $gravatar = App\Tools\Gravatar::getGravatar(Auth::user()->email);

        ?>
        <the-navbar csid="{{App\Tools\Crypt::decrypt(session('companyselected.id'))}}" is_admin="{{session('is_admin')}}" :session_user_company="{{ json_encode(session('companyselected')) }}" :usuario="{{ Auth::user()->toJson() }}" csname="{{session('companyselected.name')}}" cslogo="{{session('companyselected.logo')}}" :gravatar="{{ json_encode($gravatar)}}" base_url="{{ App::make('url')->to('/') }}"></the-navbar>
        <div class="container-fixed ">
            <the-sidebar :session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}" :user="{{ Auth::user()->toJson() }}" :restriction="{{json_encode(session('restriction'))}}" is_admin="{{session('is_admin')}}" current="{{ Route::currentRouteName() }}" base_url="{{ App::make('url')->to('/') }}" :company="`{{ json_encode(session('companyselected')) }}`" :user_departments_id="`{{ json_encode(session('user_departments_id')) }}`" ></the-sidebar>
            <main id="main" class="mini">
            <b-container>
                <div class="flex-fill px-sm-0 py-3 py-sm-5">
                    @yield('content')
                </div>
            </b-container>
            {{-- <tab-global  :session_user_permissions="{{ json_encode(session('restriction')) }}"  :restriction="{{json_encode(session('restriction'))}}" company_selected="{{ json_encode(session('companyselected')) }}" :session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"  :user="{{ Auth::user()->toJson() }}"></tab-global> --}}
            </main>
        </div>
    </div>
    @endif
</body>
<!-- <script src="{{ asset('js/app.js?version='.config('app.version')) }}"></script> -->
<script src="{{ mix('js/app.js') }}"></script>

<script>

    if(window.location.pathname == '/home'){
        var buttonURL = '';
        var videoURL = '';
        var text = '';
        var description = '';
        var Learn_More = '';
        var Close_Fullscreen = '';
        axios.get('/about-me')
        .then(res => {
            if(res.data.language == 'es_ES'){
                videoURL = 'https://videomng.builderall.com/embed/D4wd3knRlx?controls=1&allowpause=1';
                buttonURL= 'https://kbspanish.builderallwp.com/?docs=configurando-tu-chat-de-soporte-a-clientes-helpdesk';
                thumbURL= '/images/thumb/30.png';
                text = 'Introducción';
                description = 'Crea rápidamente Chats de soporte y tickets con departamentos en unidades de negocio con HelpDesk de Builderall.';
                Learn_More = 'Ver más';
                Close_Fullscreen = 'Cerrar Pantalla';
            }else if(res.data.language == 'en_US'){
                videoURL = 'https://videomng.builderall.com/embed/gV967ixUq0/?controls=1&allowpause=1';
                buttonURL = 'https://knowledgebase.builderall.com/?doc_category=helpdesk';
                thumbURL= '/images/thumb/31.png';
                text = 'Overview';
                description = 'Build your own support chat and ticket systems with their own departments in a business oriented support platform.';
                Learn_More = 'Learn More';
                Close_Fullscreen = 'Close Fullscreen';
            }

            console.log(window.location.hostname);

            if(res.data.language != 'pt_BR' && window.location.hostname == 'hs.builderall.com' || window.location.hostname == 'localhost'){
                (function(d) {
                var script = d.createElement('script');
                script.type = 'text/javascript';
                script.defer = true;
                // script.onload = function(){};
                script.src = 'https://office.builderall.com/internacional/public/mix/web-components/js/wc-tutorial-video.js?v=2';
                d.getElementsByTagName('head')[0].appendChild(script);
                }(document));

                const element = document.createElement('btn-overview');
                element.setAttribute('data-btn-text', text);
                element.setAttribute('data-box-opened', '0');
                element.setAttribute('data-box-description', description);
                element.setAttribute('data-box-button-link', buttonURL);
                element.setAttribute('data-box-button-text', Learn_More);
                element.setAttribute('data-box-thumb', thumbURL);
                element.setAttribute('data-modal-opened', '0');
                element.setAttribute('data-modal-close-text', Close_Fullscreen);
                element.setAttribute('data-modal-video-url', videoURL);
                document.getElementsByTagName('body')[0].appendChild(element);
            }
        });

    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
</script>
</html>
