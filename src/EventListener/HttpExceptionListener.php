<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsEventListener(event: ExceptionEvent::class)]
class HttpExceptionListener
{
    public function __construct(
        private readonly LoggerInterface $logger
    )
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->logger->notice($exception->getMessage());

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