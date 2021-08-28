<?php

namespace Fd\HslBundle\Event;

use Fd\HslBundle\Exception\DtoValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class DtoValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if(!$exception instanceof DtoValidationException)
        {
            return;
        }

        $data = [
            'message' => $exception->getMessage(),
            'context' => $exception->getContext()
        ];

        $response = new JsonResponse($data, $exception->getStatusCode());

        $event->setResponse($response);
    }
}