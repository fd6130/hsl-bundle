<?php

namespace Fd\HslBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonResponseException extends HttpException
{
    /**
     * @param null|string      $errorType Type of error
     * @param null|string|array     $message  Message for this error
     * @param int        $statusCode     Http status code for this error
     */
    public function __construct($errorType = null, $message = null, int $statusCode = 400)
    {
        $response = [
            'errorType' => $errorType,
            'message' => $message
        ];

        parent::__construct($statusCode,json_encode($response), null, [] , 0);
    }
}