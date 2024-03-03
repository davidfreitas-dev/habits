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

$app->get('/summary', function (Request $request, Response $response) {
  
  $results = Habit::getSummary();

  $response->getBody()->write(json_encode($results));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
      
});

$app->get('/habits/day', function (Request $request, Response $response) {

  $data = $request->getParsedBody();

  $date = $data['date'];
  
  $results = Habit::list($date);

  $response->getBody()->write(json_encode($results));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
      
});

$app->post('/habits/save', function (Request $request, Response $response) {

  $data = $request->getParsedBody();

  $title = $data['title'];

  $weekDays = $data['weekDays'];

  $results = Habit::save($title, $weekDays);

  $response->getBody()->write(json_encode($results));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

});

$app->put('/habits/{id}/toggle', function (Request $request, Response $response) {

  $idhabit = $request->getAttribute('id');

  $results = Habit::toggle($idhabit);

  $response->getBody()->write(json_encode($results));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

});

$app->run();