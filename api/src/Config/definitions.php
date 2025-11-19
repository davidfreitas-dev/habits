<?php

use App\DB\Database;
use App\Mail\Mailer;
use App\Models\User;
use App\Models\Habit;
use App\Services\AuthService;
use App\Services\MailService;
use App\Services\TokenService;
use App\Services\ErrorLogService;
use App\Interfaces\MailerInterface;
use App\Middleware\GlobalErrorHandler;

return [

  // Instância única de Database para toda a aplicação
  Database::class => DI\create(Database::class),

  // AuthService injeta automaticamente Database e TokenService
  AuthService::class => DI\autowire(AuthService::class),

  // TokenService como singleton
  TokenService::class => DI\autowire(TokenService::class),

  // Interface → implementação (Mailer será injetado onde MailerInterface é pedido)
  MailerInterface::class => DI\autowire(Mailer::class),

  // MailService recebe MailerInterface automaticamente
  MailService::class => DI\autowire(MailService::class),

  // User recebe Database via autowire (sem passar no construtor manualmente)
  User::class => DI\autowire()->constructor(DI\get(Database::class)),

  // Habit recebe Database via autowire (sem passar no construtor manualmente)
  Habit::class => DI\autowire()->constructor(DI\get(Database::class)),

  ErrorLogService::class => DI\autowire(),

  GlobalErrorHandler::class => DI\autowire(),
  
];
