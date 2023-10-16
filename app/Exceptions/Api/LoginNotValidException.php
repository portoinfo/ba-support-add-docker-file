<?php

namespace App\Exceptions\Api;

use Exception;

class LoginNotValidException extends Exception
{
    protected $message = 'Email and Password don\'t match.';

    public function render()
    {
        return response()->json([
            'error'    => class_basename($this),
            'message'  => $this->getMessage(),
        ], 401);
    }
}
