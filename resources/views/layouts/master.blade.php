<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    {{-- <meta name="user-lang" content="pt_BR"> --}}
     <meta name="user-lang" content="en_US">
    <title>Builderall</title>
    <link href="{{ asset('ico.ico') }}" rel="icon">
    
    <!-- Google Analytics -->
    @extends('plugins.google-analytics')

    @include('layouts.imports')

</head>

<body style="background-color: #F2F2F2;font-family: 'Muli' ">

    @include('layouts.menu')

    <div class="row" style="margin: 0px;padding: 0px;">
        <div class="col-sm-1" style="margin: 0px;padding: 0px;max-width: 90px;">
            @include('layouts.sidebar')
        </div>

        <div class="col" style="margin: 0px;padding: 0px;">
            @yield('content')
        </div>
        
    </div>

{{--     @include('sweetalert::alert') --}}

{{--     @if(Request::segment(1) != 'tickets' && Request::segment(1) != 'suport')
        @include('layouts.footer')
    @endif --}}

    <!-- <script src="{{ asset('js/app.js') }}"></script> -->
    <script src="{{ mix('js/app.js') }}"></script>

<script>
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
</body>
</html>
