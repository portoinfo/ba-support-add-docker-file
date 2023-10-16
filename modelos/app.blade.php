<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">

    <title>@yield('title') | Builderall Booking</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">

    <!-- <link href="{{ mix('css/app.css?version='.config('app.version')) }}" rel="stylesheet"> -->
    <link rel="manifest" href="{{ asset('manifest.json') }}" />

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:400,500,600,700,800,900&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/images/meta/favicon.png') }}">
    <meta name="theme-color" content="#0080fc" />

</head>

<body>

    @if ($fullpage ?? '')

    <div id="app">
        @yield('content')
    </div>

    @else

    <div id="app">
        <?php
            $gravatar = App\Tools\Gravatar::getGravatar(Auth::user()->email);
        ?>
        <the-navbar :usuario="{{ Auth::user()->toJson() }}" :gravatar="{{ json_encode($gravatar)}}" base_url="{{ App::make('url')->to('/') }}"></the-navbar>

        <div class="container-fixed">

            <the-sidebar
                current="{{ Route::currentRouteName() }}"
                :pending="{{ Auth::user()->getScheduleCountByStatus() }}"
                base_url="{{ App::make('url')->to('/') }}"
                :company="`{{ json_encode(session('companyselected')) }}`"
                :user_departments_id="`{{ json_encode(session('user_departments_id')) }}`"
                :session_user_cucd="{{ json_encode(session('company_user_company_departments')) }}"
            >
            </the-sidebar>

            <main id="main">

            <b-container>
                <div class="flex-fill px-sm-2 py-4 py-sm-5">
                    @yield('content')
                </div>
            </b-container>

            </main>

        </div>

    </div>

    @endif

</body>

<!-- <script src="{{ asset('js/app.js?version='.config('app.version')) }}"></script> -->
<script src="{{ mix('js/app.js') }}"></script>
</html>
