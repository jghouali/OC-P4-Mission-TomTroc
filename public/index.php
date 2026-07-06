<?php

declare(strict_types=1);

use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Settings\Settings;

require dirname(__DIR__) . '/config/environment.php';
require_once ROOT_DIR . '/vendor/autoload.php';

Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
Settings::initialize();

$router = Settings::getRouter();

$router->register('GET', '/', Settings::getHomeController()->showHomePage(...));

$router->register('GET', '/available-books', function () {
    return Settings::getBookController()->showAvailableBooks();
});
$router->register('GET', '/book-detail', function (array $params) {
    return Settings::getBookController()->showBookDetail((int) $params['id']);
});

$router->register('GET', '/login', function () {
    return Settings::getMemberController()->showLogin();
});
$router->register('GET', '/logout', function () {
    return Settings::getMemberController()->logout();
});
$router->register('POST', '/login', function (array $params) {
    return Settings::getMemberController()->login($params['email'], $params['password']);
});

$router->register('GET', '/register', function () {
    return Settings::getMemberController()->showRegister();
});
$router->register('GET', '/my-profile', function () {
    return Settings::getMemberController()->showMyProfile();
});

$router->register('GET', '/my-box', function () {
    return Settings::getMessageController()->showMyBox();
});

$request = new Request($_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'], $_POST);

$content = $router->resolve($request);

$content->send();
