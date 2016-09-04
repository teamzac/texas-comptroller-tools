<?php

namespace TeamZac\TexasComptroller\Exceptions;

class BadResponse extends \Exception
{
    /**
     * Constructor
     */
    public function __construct($errorCode)
    {
        $this->message = sprintf("The request returned a %s error", $errorCode);
    }
}