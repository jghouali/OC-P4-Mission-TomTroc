<?php

declare(strict_types=1);

namespace Test;

use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Router\Router;
use PHPUnit\Framework\TestCase;

class HomeTest extends TestCase
{
    public function testIndexPageSendAValidHttpResponseWithRouteParametersAndValuesOnContent()
    {
        $route = '/';
        $parameters = 'param';
        $value = 'value';

        $uri = "$route?$parameters=$value";

        $router = new Router();
        $request = new Request($uri);

        $response = $router->resolve($request);

        $this->assertSame(200, $response->getHttpCode());
        $this->assertSame($uri, $response->getHttpContent());
    }

    public function testUnknownPageSendAValidHttp404ResponseWithRouteParametersAndValuesOnContent()
    {
        $route = '/home';
        $parameters = 'param';
        $value = 'value';

        $uri = "$route?$parameters=$value";

        $router = new Router();
        $request = new Request($uri);

        $response = $router->resolve($request);

        $this->assertSame(404, $response->getHttpCode());
        $this->assertSame('/home not found', $response->getHttpContent());
    }
}
