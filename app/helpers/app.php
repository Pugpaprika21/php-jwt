<?php

use App\Foundation\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

/* 
* @override Slim
*/

if (!function_exists('route_name')) {

    /**
     * #route_name($request, 'user.login');
     * 
     * @param Request $request
     * @param string $routeName
     * @param array $data
     * @param array $queryParams
     * @return string
     */
    function route_name(Request $request, string $routeName, array $data = [], array $queryParams = []): string
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $routeParser->urlFor($routeName, $data, $queryParams);
    }
}

if (!function_exists('base_uri')) {

    /**
     * #base_uri($request);
     *
     * @param Request $request
     * @return string
     */
    function base_uri(Request $request): string
    {
        $uriObj = $request->getUri();

        $fullURl = $uriObj->getScheme() . '://' . $uriObj->getHost() . ':' . $uriObj->getPort() .  $uriObj->getPath();
        return $fullURl;
    }
}

if (!function_exists('throw_if')) {

    /**
     * @param bool $boolean
     * @param string $exception
     * @param string $message
     * @return void
     */
    function throw_if($boolean, $exception, $message = '')
    {
        if ($boolean) {
            throw (is_string($exception) ? new $exception($message) : $exception);
        }
    }
}

if (!function_exists('throw_unless')) {

    /**
     * @param bool $boolean
     * @param string $exception
     * @param string $message
     * @return void
     */
    function throw_unless($boolean, $exception, $message)
    {
        if (!$boolean) {
            throw (is_string($exception) ? new $exception($message) : $exception);
        }
    }
}

if (!function_exists('view')) {

    /**
     * @param Response $response
     * @param string $viewPath
     * @param array $viewVars
     * @return string|throw error
     */
    function view(Response $response, string $viewPath, array $viewVars = [])
    {
        $realPath = __DIR__ . "/../../view/pages/{$viewPath}";

        if (!file_exists($realPath)) {
            throw new Exception("View path not found: {$realPath}");
        }

        $process = function () use ($response, $realPath, $viewVars) {
            $appView = array_merge([
                'response' => $response
            ], $viewVars);
            
            extract($appView);

            try {
                ob_start();
                require $realPath;
                return ob_get_clean();
            } catch (Exception $e) {
                ob_end_clean();
                throw new Exception("Error while rendering view: {$realPath}");
            }
        };

        $response->getBody()->write($process());
        return $response;
    }
}

if (!function_exists('redirect')) {

    /**
     * @param Request $request
     * @param Response $response
     * @param string $routeName
     * @param array $queryParams
     * @param array $data
     * @return mixed
     */
    function redirect(Request $request, Response $response, string $routeName, array $queryParams = [], array $data = [])
    {
        return $response
            ->withHeader('Location', route_name($request, $routeName, $data, $queryParams))
            ->withStatus(302);
    }
}

if (!function_exists('json')) {

    /**
     * @param Response $response
     * @param array $data
     * @param integer $status
     * @return Response
     */
    function json(Response $response, array $data = [], int $status = 201): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}

if (!function_exists('image_url')) {

    /**
     * @param string $imagePath
     * @return string
     */
    function image_url(string $imagePath)
    {
        $fileExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
        $imageContents = file_get_contents($imagePath);
        $imageBase64 = base64_encode($imageContents);
        return "data:image/{$fileExtension};base64,{$imageBase64}";
    }
}

if (!function_exists('asset')) {

    /**
     * @param string $routePath
     * @return string
     * @throws InvalidArgumentException
     */
    function asset(string $routePath)
    {
        if (strpos($routePath, '..') !== false || strpos($routePath, '/') === 0) {
            throw new InvalidArgumentException("Invalid routePath");
        }

        $encodedRoutePath = urlencode($routePath);
        return $GLOBALS['APP_ENV']['APP_URL'] . "public/" . $encodedRoutePath;
    }
}
