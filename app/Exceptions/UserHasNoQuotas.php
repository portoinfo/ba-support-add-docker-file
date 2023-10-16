<?php

namespace App\Exceptions;

use Exception;

class UserHasNoQuotas extends Exception
{
    protected $message = 'User has no more quotas.';

    public function render()
    {
        return response()->json([
            'error'    => class_basename($this),
            'message'  => $this->getMessage(),
        ], 426);
    }
}
