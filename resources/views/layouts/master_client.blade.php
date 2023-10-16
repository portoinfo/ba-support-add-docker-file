<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Builderall</title>
    <link href="{{ asset('ico.ico') }}" rel="icon">
    
    @include('layouts.imports')

</head>

<body>

    @include('layouts.menu_client')

    <div class="row" style="margin: 0px;padding: 0px;">
        <div class="col" style="margin: 0px;padding: 0px;">
            @yield('content')
        </div>
    </div>

{{--     @include('sweetalert::alert') --}}

{{--     @if(Request::segment(1) != 'tickets' && Request::segment(1) != 'suport')
        @include('layouts.footer')
    @endif --}}

    <!-- <script src="{{ asset('js/app.js?version='.config('app.version')) }}"></script> -->
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
