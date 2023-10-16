<?php

namespace App\Exceptions;

use Exception;

class DomainAlreadyRegisteredException extends Exception
{
    protected $message = 'Domain already registered for company.';

    public function render()
    {
        return response()->json([
            'error'       => class_basename($this),
            'message'     => $this->getMessage(),
            'translation' =>'bs-domain-unique',
        ], 422);
    }
}
