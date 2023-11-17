<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * @session Middleware
 */

return [
    'start' => function (Request $request, RequestHandler $handler): Response {
        $options = [];
        session_cache_limiter(false);
        session_start($options);
        $response = $handler->handle($request);
        return $response;
    },
    'destroy' => function (Request $request, RequestHandler $handler): Response {
        session_unset();
        session_destroy();
        $response = $handler->handle($request);
        return $response;
    }
];