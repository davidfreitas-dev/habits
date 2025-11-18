<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CorsMiddleware {

  private array $allowedOrigins = [
    'https://habits.davidfreitas.dev.br',
    'capacitor://localhost',      // app mobile – recomendado
    'ionic://localhost',          // webview ionic
    'http://localhost',           // ambiente dev 
    'http://localhost:8100',      // ionic serve (opcional)
  ];
    
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    
    $origin = $request->getHeaderLine('Origin');

    $allowOrigin = in_array($origin, $this->allowedOrigins) ? $origin : 'null';

    if ($request->getMethod() === 'OPTIONS') {
        
      $response = new \Slim\Psr7\Response(204);
      
    } else {
        
      $response = $handler->handle($request);
      
    }

    return $response
      ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
      ->withHeader('Vary', 'Origin')
      ->withHeader('Access-Control-Allow-Credentials', 'true')
      ->withHeader('Access-Control-Allow-Headers', 'Origin, X-Token, Content-Type, Authorization')
      ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
  
  }

}
