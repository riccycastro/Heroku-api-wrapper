<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionListener
{
    /**
     * @var string
     */
    private $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $responseData = [
            'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'detail' => 'internal server error',
        ];

        if (!($exception instanceof HttpException)) {
            $event->setResponse(new JsonResponse($responseData, Response::HTTP_INTERNAL_SERVER_ERROR));
        }

        $responseData = [
            'status_code' => $exception->getStatusCode(),
            'detail' => $exception->getMessage(),
        ];

        $event->setResponse(new JsonResponse($responseData, $exception->getStatusCode()));
    }
}
