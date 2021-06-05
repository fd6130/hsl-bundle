<?php

namespace Fd\HslBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class DtoValidationException extends HttpException
{
    /**
     * @param null|string|array     $message  Message for this error
     */
    public function __construct($message = null)
    {
        parent::__construct(400, json_encode($message), null, [] , 0);
    }
}