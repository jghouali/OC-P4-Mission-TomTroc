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

$router->register('GET', '/available-books', Settings::getBookController()->showBooks(...));
$router->register('GET', '/book-detail', Settings::getBookController()->showBookDetail(...));
$router->register('GET', '/book-edit', Settings::getBookController()->showBookEdit(...));
$router->register('POST', '/book-edit', Settings::getBookController()->bookUpdate(...));
$router->register('GET', '/book-delete', Settings::getBookController()->bookDelete(...));

$router->register('GET', '/login', Settings::getMemberController()->showLogin(...));
$router->register('GET', '/logout', Settings::getMemberController()->logout(...));
$router->register('POST', '/login', Settings::getMemberController()->login(...));

$router->register('GET', '/register', Settings::getMemberController()->showRegister(...));
$router->register('GET', '/my-profile', Settings::getMemberController()->showMyProfile(...));
$router->register('POST', '/my-profile', Settings::getMemberController()->modifyMyProfile(...));
$router->register('GET', '/profile', Settings::getMemberController()->showProfile(...));

$router->register('GET', '/my-box', Settings::getMessageController()->showMyBox(...));
$router->register('POST', '/my-box', Settings::getMessageController()->sendMessage(...));
$router->register('GET', '/message-read', Settings::getMessageController()->setReadtoAllMessageByUser(...));

$request = new Request($_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'], $_POST);

$content = $router->resolve($request);

$content->send();
