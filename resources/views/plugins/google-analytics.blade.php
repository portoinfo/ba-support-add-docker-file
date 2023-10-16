@if(app('env') == 'production') 
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-X9DR3F6FS9"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-X9DR3F6FS9');
    </script>

    <!-- scripts customizados para cada view -->
    @yield('custom-ga')
@endif