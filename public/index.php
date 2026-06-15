<?php

declare(strict_types=1);

use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Router\Router;

require dirname(__DIR__) . '/config/environment.php';
require_once ROOT_DIR . '/vendor/autoload.php';

$router = new Router();

$request = new Request($_SERVER['REQUEST_URI']);

$content = $router->resolve($request);

$content->send();
