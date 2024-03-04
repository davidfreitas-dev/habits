<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use App\Model\Habit;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');

$dotenv->load();

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->add(new BasePathMiddleware($app));

$app->addErrorMiddleware(true, true, true);

$app->post('/habits/create', function (Request $request, Response $response) {

  $payload = $request->getParsedBody();

  $results = Habit::create($payload);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);

});

$app->post('/habits/summary', function (Request $request, Response $response) {
  
  $payload = $request->getParsedBody();

  $results = Habit::summary($payload['userId']);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
      
});

$app->post('/habits/day', function (Request $request, Response $response) {

  $payload = $request->getParsedBody();
  
  $results = Habit::list($payload);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
      
});

$app->put('/habits/{id}/toggle', function (Request $request, Response $response) {

  $payload = $request->getParsedBody();
  
  $habitId = $request->getAttribute('id');

  $results = Habit::toggle($payload['userId'], $habitId);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);

});

$app->run();