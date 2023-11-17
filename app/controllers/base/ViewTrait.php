<?php

namespace App\Controllers\Base;

use App\Foundation\View;
use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

trait ViewTrait
{
    public static function view()
    {
        $view = new View();
        if ($view instanceof View) {
            return $view;
        }

        throw new Exception("Error Processing Request Class View", 1);
    }

    /**
     * Twig View
     * 
     * @param Request $request
     * @return Twig 
     */
    public function twigGet(Request $request): Twig
    {
        return Twig::fromRequest($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $routeName
     * @param array $queryParams
     * @param array $data
     * @return mixed
     */
    protected function routeRedirect(Request $request, Response $response, string $routeName, array $queryParams = [], array $data = [])
    {
        return $response
            ->withHeader('Location', route_name($request, $routeName, $data, $queryParams))
            ->withStatus(302);
    }
}



