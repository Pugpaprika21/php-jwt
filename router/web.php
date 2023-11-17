<?php

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '../../src/dependency.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$app->group('/public', function (RouteCollectorProxy $group) use ($serveStatic) {
    $group->get('/html[/{folder}[/{fileName}]]', $serveStatic['html']);
    $group->get('/css[/{folder}[/{fileName}]]', $serveStatic['css']);
    $group->get('/js[/{folder}[/{fileName}]]', $serveStatic['js']);
    $group->get('/upload[/{folder}[/{fileName}]]', $serveStatic['upload']);
});

$app->get('/', function (Request $request, Response $response, array $args) {
    return view($response, 'welcome.phtml');
});

$app->run();


