<?php

namespace Fd\HslBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class DtoValidationException extends HttpException
{
    /**
     * @param string|array $message 
     */
    public function __construct($message = '')
    {
        parent::__construct(400, is_array($message) ? json_encode($message) : $message, null, [] , 0);
    }
}