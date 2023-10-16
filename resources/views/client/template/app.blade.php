<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no" id="root">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimal-ui, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="google" content="notranslate" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    @extends('plugins.google-analytics')
    @if(Request::segment(3) == 'login' || Request::segment(3) == 'login-new' || Request::segment(3) == 'register' || Request::segment(3) == 'register-new' ||
        Request::segment(1) == 'blocked' || Request::segment(1) == 'change-password')
        <meta name="user-lang" content="en_US">
    @elseif(Request::segment(1) == 'client' || Request::segment(1) == 'client-chat' || Request::segment(1) == 'client-ticket')
        <meta name="user-lang" content="{{ session('user')->language }}">
    @else
        <meta name="user-lang" content="{{ Auth::user()->language }}">
    @endif
    <!-- iOS Safari -->
    <link rel="apple-touch-icon" href="{{ asset('images/bs_apple_icons/bs_1024.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('images/bs_apple_icons/bs_120.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/bs_apple_icons/bs_152.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('images/bs_apple_icons/bs_167.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/bs_apple_icons/bs_180.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Muli:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/style-client-module.css') }}" rel="stylesheet">
    {{-- <link rel="manifest" href="{{ asset('client-manifest.json') }}" /> --}}
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <title>BA Helpdesk</title>
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        html::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        html {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>
</head>

<body>
    <div id="client-app">
        @yield('content')
        <v-overlay :value="$store.state.overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
    </div>

    <script src=" {{ mix('js/client-app.js') }} "></script>
    <link href="{{ mix('css/main.css') }}" rel="stylesheet">
</body>

</html>
