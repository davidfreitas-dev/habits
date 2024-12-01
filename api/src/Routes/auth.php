<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Auth;

$app->post('/signup', function (Request $request, Response $response) {
 
  $payload = $request->getParsedBody();

  $results = Auth::signup($payload);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);
 
});

$app->post('/signin', function (Request $request, Response $response) {

  $payload = $request->getParsedBody();

  $results = Auth::signin($payload);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);

});

$app->post('/forgot', function (Request $request, Response $response) {
 
  $payload = $request->getParsedBody();

  $results = Auth::getForgotToken($payload['email']);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);
 
});

$app->post('/forgot/token', function (Request $request, Response $response) {
 
  $payload = $request->getParsedBody();

  $results = Auth::validateForgotToken($payload['token']);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);
 
});

$app->post('/forgot/reset', function (Request $request, Response $response) {
 
  $payload = $request->getParsedBody();

  $results = Auth::setNewPassword($payload);

  $response->getBody()->write(json_encode($results));

  return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus($results['code']);
 
});