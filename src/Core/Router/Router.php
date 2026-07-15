<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Router;

use Closure;
use Exception;
use Green\TomTroc\Controller\ErrorController;
use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Http\Response;
use ReflectionFunction;
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

        // if no defaut route is set, set the default route to return the request URI
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

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function register(string $httpMethod, string $route, Closure $function)
    {
        $allowedMethods = [
            'GET',
            'POST',
        ];

        // throw an exception with Method not in the whitelist
        if (!in_array($httpMethod, $allowedMethods, true)) {
            throw new RuntimeException(
                "Trying to register an invalid Http Method : '$httpMethod'<br>Allowed method : " .
                    implode(', ', $allowedMethods) . '<br>',
                400
            );
        }

        // throw an exception if location don't start with a '/' or if contain unwanted character '
        if (!preg_match('/^\/[\-\_a-zA-Z0-9]+(?:\/[\-\_a-zA-Z0-9]+)*\/?$/', $route) && $route != '/') {
            throw new RuntimeException(
                'Trying to register an invalid Http Location',
                400
            );
        }

        $this->routes[$route][$httpMethod] = $function;
    }

    public function resolve(Request $request): Response
    {
        try {
            $location = $request->getHttpLocation();
            $method = $request->getHttpMethod();

            if (key_exists($location, $this->routes)) {
                if (key_exists($method, $this->routes[$location])) {
                    // use ReflectionFunction to inspect if needed parameters is present in the request
                    $reflexiveFunction = new ReflectionFunction($this->routes[$location][$method]);

                    $reflexiveParameters = $reflexiveFunction->getParameters();
                    $givenParameters = $request->getHttpParameters(true);

                    $args = [];
                    foreach ($reflexiveParameters as $parameter) {
                        $parameterName = $parameter->getName();
                        if (isset($givenParameters[$parameterName]) && $givenParameters[$parameterName] !== '') {
                            $args[$parameterName] = $givenParameters[$parameterName];
                        } else {
                            if ($parameter->isOptional()) {
                                continue;
                            } else {
                                // if have default value fill it
                                if ($parameter->isDefaultValueAvailable()) {
                                    $args[$parameterName] = $parameter->getDefaultValue();
                                } else {
                                    throw new RuntimeException("parameter $parameterName is not present", 400);
                                }
                            }
                        }
                    }

                    // spray args in the closure, execute it and return the result in $content
                    $content = $this->routes[$location][$method](...$args);

                    // if is a string return a new Response with code 200
                    if (is_string($content)) {
                        return new Response($content, 200);
                    }
                    // it is already a Response so return it
                    return $content;
                } else {
                    throw new RuntimeException("Method '$method' Not Allowed on route '$location'", 405);
                }
            } else {
                throw new RuntimeException('Page ' . $request->getHttpLocation() . ' not found', 404);
            }
        } catch (Exception $exception) {
            // if an exception is thrown throw to the errorController
            $response = $this->errorController->handleException($exception);
            return $response;
        }
    }
}
