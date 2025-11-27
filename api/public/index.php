<?php

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

use App\Utils\Responder;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use App\Middleware\CorsMiddleware;
use App\Middleware\GlobalErrorHandler;
use Selective\BasePath\BasePathMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);

$dotenv->load();

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions(APP_ROOT . '/src/Config/definitions.php');

$container = $containerBuilder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->add(new BasePathMiddleware($app));

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware($_ENV['APP_DEBUG'] === 'true', true, true);

$errorMiddleware->setDefaultErrorHandler($container->get(GlobalErrorHandler::class));

$app->add(new CorsMiddleware());

$app->add(new Tuupola\Middleware\JwtAuthentication([
  "path" => "/",
  "ignore" => ["/($|/)", "/signin", "/signup", "/forgot", "/verify", "/reset"],
  "secret" => $_ENV['JWT_SECRET_KEY'],
  "algorithm" => "HS256",
  "attribute" => "jwt",
  "error" => function ($response, $arguments) {
    return Responder::error($arguments["message"], 401);
  }
]));

$app->get('/', function (Request $request, Response $response) {

  return Responder::success('Welcome to the Habits API!');

});

require APP_ROOT . '/src/Routes/auth.php';
require APP_ROOT . '/src/Routes/habit.php';
require APP_ROOT . '/src/Routes/user.php';

$app->run();
