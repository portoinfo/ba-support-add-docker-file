<?php

namespace App\Exceptions;

use Exception;

class OperationNotAllowedException extends Exception
{
    protected $message = 'Operation not allowed.';

    public function render()
    {
        return response()->json([
            'error'    => class_basename($this),
            'message'  => $this->getMessage(),
        ], 401);
    }
}
