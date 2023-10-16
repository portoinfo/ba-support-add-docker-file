<?php

namespace App\Exceptions;

use Exception;

class RouteNotAllowedClient extends Exception
{
    protected $message = 'Route not allowed for a client account.';

    public function render()
    {
        return response()->json([
            'error'    => class_basename($this),
            'message'  => $this->getMessage(),
            'redirect' => route('logout-client'),
        ], 422);
    }
}
