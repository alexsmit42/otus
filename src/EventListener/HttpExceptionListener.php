<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsEventListener(event: ExceptionEvent::class)]
class HttpExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (
            $exception instanceof HttpException
            //&& $exception->getPrevious() instanceof ValidationFailedException
        ) {
            $event->setResponse(
                new JsonResponse(
                    ['success' => false, 'error' => $exception->getMessage()],
                    $exception->getStatusCode()
                )
            );
        }
    }
}