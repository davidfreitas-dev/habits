<?php

namespace App\Middleware;

use Throwable;
use App\Utils\ApiResponse;
use Slim\Interfaces\ErrorHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;

class GlobalErrorHandler implements ErrorHandlerInterface
{
    
  public function __invoke(
    $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
  ): Response {

    $status = $this->normalizeStatusCode($exception->getCode());

    $error = ApiResponse::error($exception->getMessage(), $status);

    $response = new \Slim\Psr7\Response();
    
    $response->getBody()->write(json_encode($error));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);

  }

  private function normalizeStatusCode(int $code): int
  {
      
    return ($code >= 100 && $code < 600) ? $code : 500;
    
  }

}
