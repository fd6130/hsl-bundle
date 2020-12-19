<?php

namespace Fd\HslBundle\Event\Listener;

use App\Exception\CustomJsonExceptionInterface;
use Fd\HslBundle\Exception\JsonResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class JsonResponseExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if(!$exception instanceof JsonResponseException)
        {
            return;
        }

        // Customize your response object to display the exception details
        $response = new JsonResponse($exception->getMessage(), $exception->getStatusCode(), $exception->getHeaders(), true);

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}