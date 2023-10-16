<?php

namespace App\Exceptions;

use Exception;

class DomainNotValidException extends Exception
{
    protected $message = 'Domain is not valid.';

    public function render()
    {
        return response()->json([
            'error'       => class_basename($this),
            'message'     => $this->getMessage(),
            'translation' => 'bs-domain-is-not-valid',
        ], 422);
    }
}
