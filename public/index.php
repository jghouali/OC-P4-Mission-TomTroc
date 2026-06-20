<?php

declare(strict_types=1);

use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Settings\Settings;

require dirname(__DIR__) . '/config/environment.php';
require_once ROOT_DIR . '/vendor/autoload.php';

Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
Settings::initialize();

$router = Settings::getRouter();
$router->register('/', function () {
    return Settings::getHomeController()->showHomePage();
});
$request = new Request($_SERVER['REQUEST_URI']);

$content = $router->resolve($request);

$content->send();
