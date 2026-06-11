<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Router;

use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Http\Response;

class Router
{
    protected array $routes;
    protected Request $request;

    public function __construct(array $routes = [])
    {
        $this->routes = $routes;
        if (!isset($this->routes['/'])) {
            $this->routes['/'] = function ($params) {
                $stringContent = '/?';
                foreach ($params as $param => $value) {
                    $stringContent = "$stringContent" . "$param=$value";
                }
                return $stringContent;
            };
        }
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

    public function pageNotFoundContent(Request $request): string
    {
        return $request->getHttpLocation() . ' not found';
    }

    public function resolve(Request $request)
    {
        if (key_exists($request->getHttpLocation(), $this->routes)) {
            $content = $this->routes[$request->getHttpLocation()]($request->getHttpParameters(true));

            return new Response($content);
        } else {
            $content = $this->pageNotFoundContent($request);
            return new Response($content, 404);
        }
    }
}
