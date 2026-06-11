<?php

declare(strict_types=1);

use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Router\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Router();

$request = new Request($_SERVER['REQUEST_URI']);

$content = $router->resolve($request);

$content->send();
