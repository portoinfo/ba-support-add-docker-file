<?php

namespace App\Exceptions\Api;

use Exception;

class CompanyNotFoundException extends Exception
{
    protected $message = 'Company not found.';

    public function render()
    {
        return response()->json([
            'error'    => class_basename($this),
            'message'  => $this->getMessage(),
        ], 404);
    }
}
