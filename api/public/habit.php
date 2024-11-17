<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Habit;

$app->post('/habits/create', function (Request $request, Response $response) {

  $payload = $request->getParsedBody();

  $results = Habit::create($payload);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);

});

$app->post('/habits/summary', function (Request $request, Response $response) {
  
  $payload = $request->getParsedBody();

  $results = Habit::summary($payload['userId']);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);
      
});

$app->post('/habits/day', function (Request $request, Response $response) {

  $payload = $request->getParsedBody();
  
  $results = Habit::list($payload);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);
      
});

$app->put('/habits/{id}/toggle', function (Request $request, Response $response) {

  $payload = $request->getParsedBody();
  
  $habitId = $request->getAttribute('id');

  $results = Habit::toggle($payload['userId'], $habitId);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);

});