<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    protected $message = 'Error en la aplicación';

    public function __construct($message = null)
    {
        if ($message) {
            $this->message = $message;
        }

        parent::__construct($this->message);
    }
}
