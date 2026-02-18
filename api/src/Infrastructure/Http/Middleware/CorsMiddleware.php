<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(private array $settings)
    {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        // Handle preflight requests
        $response = $request->getMethod() === 'OPTIONS' ? new Response() : $handler->handle($request);

        $origin = $request->getHeaderLine('Origin');
        $allowedOrigins = $this->settings['allowed_origins'];

        // Allow common development origins for Capacitor/Ionic
        $devOrigins = [
            'https://habits.davidfreitas.dev.br',
            'capacitor://localhost',
            'ionic://localhost',
            'https://localhost',
            'http://localhost',
        ];
        $allowedOrigins = array_unique(array_merge($allowedOrigins, $devOrigins));

        // Allow live-reload origins dynamically (e.g. http://localhost:8100, http://192.168.x.x:8100)
        if ($origin && (
            str_starts_with($origin, 'http://localhost:') ||
            str_starts_with($origin, 'http://127.0.0.1:') ||
            preg_match('/^http:\/\/192\.168\.\d+\.\d+(:\d+)?$/', $origin) ||
            preg_match('/^http:\/\/10\.\d+\.\d+\.\d+(:\d+)?$/', $origin)
        )) {
            $allowedOrigins[] = $origin;
        }

        // Check if origin is allowed. Also allow requests without an Origin header.
        if ($origin === '' || \in_array('*', $allowedOrigins, true) || \in_array($origin, $allowedOrigins, true)) {
            $allowOrigin = \in_array('*', $allowedOrigins, true) ? '*' : $origin;
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
                ->withHeader(
                    'Access-Control-Allow-Methods',
                    \implode(', ', $this->settings['allowed_methods']),
                )
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    \implode(', ', $this->settings['allowed_headers']),
                )
                ->withHeader(
                    'Access-Control-Expose-Headers',
                    \implode(', ', $this->settings['exposed_headers']),
                )
                ->withHeader(
                    'Access-Control-Max-Age',
                    (string)$this->settings['max_age'],
                )
            ;

            if ($this->settings['allow_credentials']) {
                $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
            }
        }

        return $response;
    }
}
