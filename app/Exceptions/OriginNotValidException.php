<?php

namespace App\Exceptions;

use Exception;

class OriginNotValidException extends Exception
{
    protected $message = 'Origin not allowed for company.';

    public function render()
    {
        return response()->json([
            'error'    => class_basename($this),
            'message'  => $this->getMessage(),
        ], 422);
    }
}
