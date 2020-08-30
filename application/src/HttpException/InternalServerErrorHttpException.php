<?php

namespace App\HttpException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class InternalServerErrorHttpException extends HttpException
{
    /**
     * @param string $message The internal exception message
     * @param Throwable $previous The previous exception
     * @param int $code The internal exception code
     * @param array $headers
     */
    public function __construct(string $message = 'Internal server error', Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $previous, $headers, $code);
    }
}
