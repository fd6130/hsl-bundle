<?php

namespace Fd\HslBundle\Event\Listener;

use Fd\HslBundle\Exception\DtoValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class DtoValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if(!$exception instanceof DtoValidationException)
        {
            return;
        }

        // Customize your response object to display the exception details
        $response = new JsonResponse($exception->getMessage(), $exception->getStatusCode(), $exception->getHeaders(), true);

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}