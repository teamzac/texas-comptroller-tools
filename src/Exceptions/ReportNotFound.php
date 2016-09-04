<?php

namespace TeamZac\TexasComptroller\Exceptions;

class ReportNotFound extends \Exception
{
    protected $message = 'The requested report was not found (a 404 was returned).';
}