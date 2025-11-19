<?php

namespace App\Middleware;

use Throwable;
use App\Utils\ApiResponse;
use App\Services\ErrorLogService;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Psr7\Response as SlimResponse;
use Psr\Http\Message\ResponseInterface as Response;

class GlobalErrorHandler implements ErrorHandlerInterface
{

  public function __construct(
    private ErrorLogService $logger,
    private bool $displayErrorDetails = false
  ) {}

  public function __invoke(
    $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
  ): Response {

    $status = $this->resolveStatus($exception->getCode());

    $message = $this->resolveMessage($exception, $status);

    if ($this->shouldLog($exception, $status)) {
      
      $this->logger->log($exception, [
        "method" => $request->getMethod(),
        "url"    => (string)$request->getUri()
      ]);
    
    }

    $error = ApiResponse::error($message, $status);

    $response = new SlimResponse();
    
    $response->getBody()->write(json_encode($error));

    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus($status);

  }

  private function resolveStatus(int|string|null $code): int
  {

    return (is_int($code) && $code >= 100 && $code < 600)
      ? $code
      : 500;
  
  }

  private function resolveMessage(Throwable $exception, int $status): string
  {

    if ($status >= 500 && !$this->displayErrorDetails) {
      
      return "Erro interno no servidor.";
      
    }

    return $exception->getMessage();

  }

  private function shouldLog(Throwable $e, int $status): bool
  {
    
    return $status >= 500
      || $e instanceof \PDOException
      || $e->getCode() === 0;

    return false;
  
  }

}
