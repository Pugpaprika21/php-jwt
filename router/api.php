<?php

use App\Foundation\Database\Query;
use Firebase\JWT\{JWT, Key};
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Service\Http\Init\Http;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '../../src/dependency.php';

define('SECRET_KEY', $GLOBALS['APP_ENV']['SECRET_KEY_JWT']);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

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

$app->group('/public', function (RouteCollectorProxy $group) use ($serveStatic) {
    $group->get('/html[/{folder}[/{fileName}]]', $serveStatic['html']);
    $group->get('/css[/{folder}[/{fileName}]]', $serveStatic['css']);
    $group->get('/js[/{folder}[/{fileName}]]', $serveStatic['js']);
    $group->get('/upload[/{folder}[/{fileName}]]', $serveStatic['upload']);
});

$query = new Query();

$app->get('/jwt/login', function (Request $request, Response $response) {
    return view($response, "login.phtml");
});

$app->get('/jwt/register', function (Request $request, Response $response) {
    return view($response, "register.phtml");
});

$app->post('/jwt/register', function (Request $request, Response $response) use ($query): Response {
    $body = $request->getParsedBody();

    $username = str($body['username']);
    $password = str($body['password']);

    $user = $query->excute("SELECT COUNT(*) AS user_exist FROM users WHERE username = '{$username}'", false);

    if ($user->user_exist == 0) {
        if ($query->table('users')->insert([
            'username' => $username,
            'password' => $password,
            'is_deleted' => false
        ])) {
            return json($response, ['message' => 'register success..'], Http::OK);
        }
    }

    return json($response, ['message' => 'register error..'], Http::SERVER_ERROR);
});

$app->post('/jwt/login', function (Request $request, Response $response) use ($query): Response {
    $body = $request->getParsedBody();

    $username = str($body['username'] ?? "");
    $password = str($body['password'] ?? "");

    $user = $query->excute("SELECT username, password, COUNT(*) AS user_exist FROM users WHERE username = '{$username}' AND password = '{$password}' GROUP BY username, password", false);
    if (!empty($user->user_exist) && $user->user_exist > 0) {
        global $genTokenJWT;
        $tokenJWT = $genTokenJWT($user->username);
        $data = [
            'username' => $user->username,
            'password' => $user->password,
            'tokenJWT' => $tokenJWT
        ];
        return json($response, $data, Http::OK);
    }
    return json($response, ['message' => 'Login Error..'], Http::SERVER_ERROR);
});

$app->post('/jwt/check-login', function (Request $request, Response $response) use ($query): Response {
    $authorization = $request->getHeader('Authorization')[0] ?? null;
    $token = substr($authorization, 7);
    if (!empty($token)) {
        $decodeJWT = JWT::decode($token, new Key(SECRET_KEY, 'HS256'));
        $usernameJWT = str($decodeJWT->username);
        $expirationTime = $decodeJWT->exp;
        $currentTimestamp = time();

        if ($currentTimestamp < $expirationTime) {
            $user = $query->excute("SELECT username, password, COUNT(*) AS user_exist FROM users WHERE username = '{$usernameJWT}' GROUP BY username, password", false);
            return json($response, ['message' => 'Token is still valid.', 'auth' => $user], Http::OK);
        } else {
            return json($response, ['message' => 'Token has expired.'], Http::SERVER_ERROR);
        }
    }
    return json($response, ['message' => 'Authorization error..'], Http::SERVER_ERROR);
});

$genTokenJWT = function (string $username): string {
    $issuedAt = time();
    $expirationTime = $issuedAt + 1 * 60;
    $currentDate = date('Y-m-d H:i:s');

    $payload = [
        'username' => $username,
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'currentDate' => $currentDate
    ];

    return JWT::encode($payload, SECRET_KEY, 'HS256');
};

$app->run();
