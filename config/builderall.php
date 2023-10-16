<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Builderall users microservice IP address ou URL
    |--------------------------------------------------------------------------
    |
    */
    'users_service' => [
        'server_addr' => env('BUILDERALL_USERS_SERVICE_SERVER_ADDR', '65.111.191.146:45746')
        // "65.111.191.146:45746"; // ambiente de testes
        // "64.251.1.123:42110"; // ambiente de produção
    ],

    /*
    |--------------------------------------------------------------------------
    | Path to redirect after login or logout
    |--------------------------------------------------------------------------
    |
    */
    // 'redirect_after_login' => env('BUILDERALL_REDIRECT_AFTER_LOGIN', '/select-company'), COMENTADO PARA ADICIONAR NO WRAPPER
    'redirect_after_login' => env('BUILDERALL_REDIRECT_AFTER_LOGIN', '/new'), 
    'redirect_after_logout' => env('BUILDERALL_REDIRECT_AFTER_LOGOUT', '/login'),
    'redirect_if_not_logged' => env('BUILDERALL_REDIRECT_IF_NOT_LOGGED', '/login'),

    /*
    |--------------------------------------------------------------------------
    | Show login form on GET in /login
    |--------------------------------------------------------------------------
    |
    | true = show
    | false = 403 error
    |
    */
    'show_login_form' => env('BUILDERALL_SHOW_LOGIN_FORM', true)
];
