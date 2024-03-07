<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Model\Auth;

$app->post('/signup', function (Request $request, Response $response) {
 
  $payload = $request->getParsedBody();

  $result = Auth::signup($payload);

  $response->getBody()->write(json_encode($result));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
 
});

$app->post('/signin', function (Request $request, Response $response) {

  $payload = $request->getParsedBody();

  $result = Auth::signin($payload);

  $response->getBody()->write(json_encode($result));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);

});

$app->post('/forgot', function (Request $request, Response $response) {
 
  $payload = $request->getParsedBody();

  $result = Auth::getForgotLink($payload['email']);

  $response->getBody()->write(json_encode($result));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(200);
 
});