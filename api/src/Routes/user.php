<?php

use App\Models\User;
use App\Utils\ApiResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/users/me', function (Request $request, Response $response, array $args) {

  $user = $this->get(User::class);

  $jwt = $request->getAttribute('jwt');

  $id = (int)$jwt['user']->id;

  $userData = $user->get($id);

  $results = ApiResponse::success('Dados do usuário.', $userData);

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);

});

$app->put('/users/me', function (Request $request, Response $response, array $args) {

  $user = $this->get(User::class);

  $jwt = $request->getAttribute('jwt');

  $requestData = $request->getParsedBody();
  
  $requestData['id'] = (int)$jwt['user']->id;

  $user->setAttributes($requestData);

  $user->update();

  $results = ApiResponse::success('Dados do usuário atualizados com sucesso.');

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);

});

$app->delete('/users/me', function (Request $request, Response $response, array $args) {

  $user = $this->get(User::class);

  $jwt = $request->getAttribute('jwt');

  $id = (int)$jwt['user']->id;

  $user->delete($id);
  
  $results = ApiResponse::success('Conta excluída com sucesso.');

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);

});