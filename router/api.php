<?php

use App\Foundation\Database\Query;
use Firebase\JWT\JWT;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '../../src/dependency.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

########################################### cors ###########################################

$app->add(function (Request $request, RequestHandler $handler): Response {
    $routeContext = RouteContext::fromRequest($request);
    $routingResults = $routeContext->getRoutingResults();
    $methods = $routingResults->getAllowedMethods();
    $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

    $response = $handler->handle($request);

    $response = $response->withHeader('Access-Control-Allow-Origin', '*');
    $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
    $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);

    return $response;
});

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

########################################### route api ###########################################

$app->group('/public', function (RouteCollectorProxy $group) use ($serveStatic) {
    $group->get('/html[/{folder}[/{fileName}]]', $serveStatic['html']);
    $group->get('/css[/{folder}[/{fileName}]]', $serveStatic['css']);
    $group->get('/js[/{folder}[/{fileName}]]', $serveStatic['js']);
    $group->get('/upload[/{folder}[/{fileName}]]', $serveStatic['upload']);
});

$query = new Query();

$app->get('/login', function (Request $request, Response $response) {
    return view($response, "login.phtml");
});

$app->get('/', function (Request $request, Response $response) use ($query) {
    $pdo = $query->usePDO();
    $users = $pdo->query("select * from users")->fetchAll();

    return json($response, $users);
});

$app->run();
