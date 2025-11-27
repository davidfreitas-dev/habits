<?php

use App\Utils\Responder;
use App\Services\AuthService;
use App\Services\MailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/signup', function (Request $request, Response $response) {

  $auth = $this->get(AuthService::class);
  
  $req = $request->getParsedBody();

  $result = $auth->signup($req);

  return Responder::success('Cadastro efetuado com sucesso.', $result, 201);
      
});

$app->post('/signin', function (Request $request, Response $response) {
  
  $auth = $this->get(AuthService::class);
  
  $req = $request->getParsedBody();

  $result = $auth->signin($req['email'], $req['password']);

  return Responder::success('Autenticação efetuada com sucesso.', $result);
      
});

$app->post('/forgot', function (Request $request, Response $response) {

  $auth = $this->get(AuthService::class);
  
  $mail = $this->get(MailService::class);
  
  $req = $request->getParsedBody();

  $result = $auth->requestPasswordReset($req['email']);

  $mail->sendPasswordReset(
    $result['user']['email'],
    $result['user']['name'],
    $result['code']
  );

  return Responder::success('E-mail de recuperação enviado com sucesso.');
      
});

$app->post('/verify', function (Request $request, Response $response) {

  $auth = $this->get(AuthService::class);
  
  $req = $request->getParsedBody();

  $auth->verifyResetToken($req['token']);

  return Responder::success('Token de recuperação validado com sucesso.');
      
});

$app->post('/reset', function (Request $request, Response $response) {

  $auth = $this->get(AuthService::class);
  
  $req = $request->getParsedBody();

  $auth->resetPassword($req['password'], $req);

  return Responder::success('Senha redefinida com sucesso.');
      
});
