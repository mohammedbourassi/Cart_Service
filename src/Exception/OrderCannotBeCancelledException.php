<?php

namespace App\Exception;

class OrderCannotBeCancelledException extends \Exception
{
    
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}