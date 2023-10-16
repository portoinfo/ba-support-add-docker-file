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
    @if (Request::segment(1) == 'login' || Request::segment(1) == 'register' || Request::segment(3) == 'login' || Request::segment(3) == 'register' || Request::segment(1) == 'blocked' || Request::segment(1) == 'change-password')
        <meta name="user-lang" content="en_US">
    @elseif(Request::segment(1) == 'client' || Request::segment(1) == 'client-chat' || Request::segment(1) ==
        'client-ticket')
        <meta name="user-lang" content="{{ Auth::user()->language }}">
    @else
        <meta name="user-lang" content="{{ Auth::user()->language }}">
    @endif
    <title>@yield('title') | Builderall - Support</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}" />

    <!-- <link href="{{ mix('css/app.css') }}" rel="stylesheet"> -->
    <!-- <link href="{{ asset('css/style.css?version=' . config('app.version')) }}" rel="stylesheet">
    <link href="{{ asset('css/chat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/css-google.css?version='.config('app.version')) }}" rel="stylesheet">
    <link href="{{ asset('css/css2-google.css?version='.config('app.version')) }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}"> 
    <!-- BUILDERALL IMPORTS NEW LAYOUT-->
        @if(config('app.name') === 'basupport' || config('app.url') === 'http://localhost')
            <link rel='stylesheet' href='https://css.kub.app.builderall.io/builderall.css'>
            <script src="https://appshell.kub.app.builderall.io/appshell.js"></script>
        @elseif(config('app.name') === 'basupportinfo')
            <link rel='stylesheet' href='https://css.kub.app.builderall.io/builderall.css'>
            <script src="https://appshell.kub.app.builderall.info/appshell.js"></script>
        @elseif(config('app.name') === 'Helpdesk' || config('app.name') === 'BA_SUPPORT')
            <link rel='stylesheet' href='https://css.builderall.com/builderall.css'>
            <script src='https://js.builderall.com/builderall.js'></script>
        @endif
    <!-- BUILDERALL IMPORTS NEW LAYOUT-->
    <link href="{{ asset('css/app_4.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('css/css3-google.css?version='.config('app.version')) }}" rel="stylesheet"> -->
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <!-- {{-- <script src="{{ asset('js/moment-with-locales.min.js') }}"></script> --}}
    <script src="{{ asset('js/sweetalert2@10.js') }}"></script>
    <script src="{{ asset('js/sorttable.js') }}"></script>
    <script src="{{ asset('js/xlsx.full.min.js') }}"></script> -->
    <script src="{{ asset('js/chart.js') }}"></script>
</head>
<body>
    <!-- <builderall-appshell></builderall-appshell> -->
    <builderall-appshell token="{{ session('tokenOffice') }}" tool="{{ session('suporteType') }}">
        <!-- <script type='module' src='builderall.appshell/dist/index.js'></script> -->
        <div id="admin-app" class='ba-app-content app4'>
            @yield('content')  
        </div> 
    </builderall-appshell>
</body>
<!-- <script src="{{ asset('js/app.js?version=' . config('app.version')) }}"></script> -->
<script src="{{ mix('js/admin-app.js') }}"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<!-- <script src="jquery.ui.touch-punch.min.js"></script> -->


<script>
    // const Toast = Swal.mixin({
    //     toast: true,
    //     position: 'top-end',
    //     showConfirmButton: false,
    //     timer: 4000,
    //     timerProgressBar: true,
    //     didOpen: (toast) => {
    //         toast.addEventListener('mouseenter', Swal.stopTimer)
    //         toast.addEventListener('mouseleave', Swal.resumeTimer)
    //     }
    // });


</script>


</html>
