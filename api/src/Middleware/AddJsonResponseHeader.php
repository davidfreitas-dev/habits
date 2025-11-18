<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AddJsonResponseHeader
{

  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    
    $response = $handler->handle($request);

    if (!$response->hasHeader('Content-Type')) {
        
      return $response->withHeader('Content-Type', 'application/json');
      
    }

    return $response;
    
  }

}
