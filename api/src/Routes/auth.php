<?php

use App\Utils\ApiResponse;
use App\Services\AuthService;
use App\Services\MailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/signup', function (Request $request, Response $response) {

  $auth = $this->get(AuthService::class);
  
  $requestData = $request->getParsedBody();

  $authData = $auth->signup($requestData);

  $results = ApiResponse::success('Cadastro efetuado com sucesso.', $authData, 201);

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);
      
});

$app->post('/signin', function (Request $request, Response $response) {
  
  $auth = $this->get(AuthService::class);
  
  $requestData = $request->getParsedBody();

  $authData = $auth->signin($requestData['email'], $requestData['password']);

  $results = ApiResponse::success('Autenticação efetuada com sucesso.', $authData);

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);
      
});

$app->post('/forgot', function (Request $request, Response $response) {

  $auth = $this->get(AuthService::class);
  
  $mail = $this->get(MailService::class);
  
  $requestData = $request->getParsedBody();

  $authData = $auth->requestPasswordReset()($requestData['email']);

  $mail->sendPasswordReset(
    $authData['user']['email'],
    $authData['user']['name'],
    $authData['code']
  );

  $results = ApiResponse::success('E-mail de recuperação enviado com sucesso.');

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);
      
});

$app->post('/verify', function (Request $request, Response $response) {

  $auth = $this->get(AuthService::class);
  
  $requestData = $request->getParsedBody();

  $auth->verifyResetToken($requestData['token']);

  $results = ApiResponse::success('Token de recuperação validado com sucesso.');

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);
      
});

$app->post('/reset', function (Request $request, Response $response) {

  $auth = $this->get(AuthService::class);
  
  $requestData = $request->getParsedBody();

  $auth->resetPassword($requestData['password'], $requestData);

  $results = ApiResponse::success('Senha redefinida com sucesso.');

  $response->getBody()->write(json_encode($results));

  return $response->withStatus($results['code']);
      
});
