<?php

use App\Models\Habit;
use App\Utils\ApiResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/habits', function (Request $request, Response $response) {

  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $requestData = $request->getParsedBody();

  $requestData['user_id'] = (int)$jwt['user']->id;

  $habit->setAttributes($requestData);

  $habitData = $habit->create();

  $results = ApiResponse::success('Hábito criado com sucesso.', $habitData, 201);

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);

});

$app->post('/habits/summary', function (Request $request, Response $response) {
  
  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $userId = (int)$jwt['user']->id;

  $summary = $habit->summary($userId);

  $results = ApiResponse::success('Resumo dos hábitos para o dia selecionado.', $summary);

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);
      
});

$app->post('/habits/day', function (Request $request, Response $response) {

  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $userId = (int)$jwt['user']->id;

  $requestData = $request->getParsedBody();

  $habits = $habit->list($userId, $requestData['date']);

  $results = ApiResponse::success('Lista de hábitos possíveis e completados.', $habits);

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);
      
});

$app->put('/habits/{id:[0-9]+}/toggle', function (Request $request, Response $response, array $args) {

  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $userId = (int)$jwt['user']->id;

  $id = (int)$args['id'];

  $habit->toggle($userId, $id);

  $results = ApiResponse::success('Hábito marcado/desmarcado com sucesso.');

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);

});

$app->get('/habits/{id:[0-9]+}', function (Request $request, Response $response, array $args) {
  
  $habit = $this->get(Habit::class);
  
  $id = (int)$args['id'];

  $habitData = $habit->get($id);

  $results = ApiResponse::success('Detalhes do hábito.', $habitData);

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);

});

$app->put('/habits/{id:[0-9]+}', function (Request $request, Response $response, array $args) {

  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $requestData = $request->getParsedBody();

  $requestData['id'] = (int)$args['id'];

  $requestData['user_id'] = (int)$jwt['user']->id;

  $habit->setAttributes($requestData);

  $habitData = $habit->update();

  $results = ApiResponse::success('Hábito atualizado com sucesso.', $habitData);

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);

});

$app->delete('/habits/{id:[0-9]+}', function (Request $request, Response $response, array $args) {

  $habit = $this->get(Habit::class);

  $id = (int)$args['id'];

  $habit->delete($id);

  $results = ApiResponse::success('Hábito excluído com sucesso.');

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);

});