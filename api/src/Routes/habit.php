<?php

use App\Models\Habit;
use App\Utils\Responder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/habits', function (Request $request, Response $response) {

  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $req = $request->getParsedBody();

  $req['user_id'] = (int)$jwt['user']->id;

  $habit->setAttributes($req);

  $result = $habit->create();

  return Responder::success('Hábito criado com sucesso.', $result, 201);

});

$app->post('/habits/summary', function (Request $request, Response $response) {
  
  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $userId = (int)$jwt['user']->id;

  $results = $habit->summary($userId);

  return Responder::success('Resumo dos hábitos para o dia selecionado.', $results);
      
});

$app->post('/habits/day', function (Request $request, Response $response) {

  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $userId = (int)$jwt['user']->id;

  $req = $request->getParsedBody();

  $results = $habit->list($userId, $req['date']);

  return Responder::success('Lista de hábitos possíveis e completados.', $results);
      
});

$app->put('/habits/{id:[0-9]+}/toggle', function (Request $request, Response $response, array $args) {

  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $userId = (int)$jwt['user']->id;

  $id = (int)$args['id'];

  $habit->toggle($userId, $id);

  return Responder::success('Hábito marcado/desmarcado com sucesso.');

});

$app->get('/habits/{id:[0-9]+}', function (Request $request, Response $response, array $args) {
  
  $habit = $this->get(Habit::class);
  
  $id = (int)$args['id'];

  $result = $habit->get($id);

  return Responder::success('Detalhes do hábito.', $result);

});

$app->put('/habits/{id:[0-9]+}', function (Request $request, Response $response, array $args) {

  $habit = $this->get(Habit::class);

  $jwt = $request->getAttribute('jwt');

  $req = $request->getParsedBody();

  $req['id'] = (int)$args['id'];

  $req['user_id'] = (int)$jwt['user']->id;

  $habit->setAttributes($req);

  $result = $habit->update();

  return Responder::success('Hábito atualizado com sucesso.', $result);

});

$app->delete('/habits/{id:[0-9]+}', function (Request $request, Response $response, array $args) {

  $habit = $this->get(Habit::class);

  $id = (int)$args['id'];

  $habit->delete($id);

  return Responder::success('Hábito excluído com sucesso.');

});