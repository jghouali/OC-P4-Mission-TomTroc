<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Router;

use Exception;
use Green\TomTroc\Controller\ErrorController;
use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Http\Response;
use RuntimeException;

class Router
{
    protected array $routes;
    protected Request $request;
    private ErrorController $errorController;

    public function __construct(ErrorController $errorController, array $routes = [])
    {
        $this->errorController = $errorController;
        $this->routes = $routes;

        if (!isset($this->routes['default'])) {
            $this->routes['default'] = function ($location, $params) {
                $stringContent = "$location?";
                foreach ($params as $param => $value) {
                    $stringContent = "$stringContent" . "$param=$value";
                }
                return $stringContent;
            };
        }
    }

    public function register(string $httpMethod, string $route, callable $function)
    {
        $allowedMethods = [
            'GET',
            'POST',
        ];

        if (!in_array($httpMethod, $allowedMethods, true)) {
            throw new RuntimeException(
                "Trying to register an invalid Method : \'$httpMethod\'<br>Allowed method : " .
                    implode(', ', $allowedMethods) . '<br>',
                500
            );
        }

        $this->routes[$route][$httpMethod] = $function;
    }

    public function pageNotFoundContent(Request $request): string
    {
        return $request->getHttpLocation() . ' not found';
    }

    public function resolve(Request $request): Response
    {
        try {
            $location = $request->getHttpLocation();
            $method = $request->getHttpMethod();

            if (key_exists($location, $this->routes)) {
                if (key_exists($method, $this->routes[$location])) {
                    $content = $this->routes[$location][$method]($request->getHttpParameters(true));

                    if (is_string($content)) {
                        return new Response($content, 200);
                    }
                    return $content;
                } else {
                    throw new RuntimeException("Method '$method' Not Allowed on route '$location'", 405);
                }
            } else {
                throw new RuntimeException("Page '$location' not found", 404);
            }
        } catch (Exception $exception) {
            $response = $this->errorController->handleException($exception);
            return $response;
        }
    }
}
