<?php

namespace App\Exceptions;

use Exception;

class RouteNotAllowedAgent extends Exception
{

    protected $message = 'Route not allowed for an agent account.';

    public function render()
    {
        return response()->json([
            'error'    => class_basename($this),
            'message'  => $this->getMessage(),
            'redirect' => route('logout'),
        ], 422);
    }
}
