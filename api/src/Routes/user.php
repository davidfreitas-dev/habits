<?php

use App\Models\User;
use App\Utils\Responder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/users/me', function (Request $request, Response $response, array $args) {

  $user = $this->get(User::class);

  $jwt = $request->getAttribute('jwt');

  $id = (int)$jwt['user']->id;

  $result = $user->get($id);

  return Responder::success('Dados do usuário.', $result);

});

$app->put('/users/me', function (Request $request, Response $response, array $args) {

  $user = $this->get(User::class);

  $jwt = $request->getAttribute('jwt');

  $requestData = $request->getParsedBody();
  
  $requestData['id'] = (int)$jwt['user']->id;

  $user->setAttributes($requestData);

  $user->update();

  return Responder::success('Dados do usuário atualizados com sucesso.');

});

$app->delete('/users/me', function (Request $request, Response $response, array $args) {

  $user = $this->get(User::class);

  $jwt = $request->getAttribute('jwt');

  $id = (int)$jwt['user']->id;

  $user->delete($id);
  
  return Responder::success('Conta excluída com sucesso.');

});