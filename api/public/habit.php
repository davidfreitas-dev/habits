<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Habit;

$app->put('/habits/{id}/toggle', function (Request $request, Response $response) {

  $data = $request->getParsedBody();
  
  $habitId = $request->getAttribute('id');

  $results = Habit::toggle($data['userId'], $habitId);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);

});

$app->put('/habits/update/{id}', function (Request $request, Response $response, array $args) {

  $data = $request->getParsedBody();

  $data['id'] = (int)$args['id'];

  $habit = new Habit();

  $habit->setAttributes($data);

  $results = $habit->update();

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);

});

$app->post('/habits/create', function (Request $request, Response $response) {

  $data = $request->getParsedBody();

  $habit = new Habit();

  $habit->setAttributes($data);

  $results = $habit->create();

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);

});

$app->post('/habits/summary', function (Request $request, Response $response) {
  
  $data = $request->getParsedBody();

  $results = Habit::summary($data['userId']);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);
      
});

$app->post('/habits/day', function (Request $request, Response $response) {

  $data = $request->getParsedBody();
  
  $results = Habit::list($data);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);
      
});