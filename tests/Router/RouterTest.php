<?php

declare(strict_types=1);

namespace Tests\Router;

use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Settings\Settings;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
    }

    public function setUp(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
    }

    #[TestDox('register() can set a callable as route and resolve() on that route return the result of the callable')]
    public function testResolveWithExistingRouteReturnAHttp200ResponseObject(): void
    {
        $testCallable = function ($param) {
            ob_start();
            var_dump($param);
            $data = ob_get_clean();
            return "LoginForm with :\n $data";
        };
        Settings::getRouter()->register('GET', '/login', $testCallable);
        $requestUri = 'GET /login?rememberme=1';
        $request = new Request($requestUri);
        $response = Settings::getRouter()->resolve($request);

        $this->assertSame('Green\TomTroc\Core\Http\Response', $response::class);
        $this->assertSame(200, $response->getHttpCode());
        $this->assertMatchesRegularExpression('/LoginForm with :/', $response->getHttpContent());
        $this->assertMatchesRegularExpression('/rememberme/', $response->getHttpContent());
    }

    #[TestDox('resolve() on an inexisting route return pageNotFoundContent()')]
    public function testPageNotFoundContent(): void
    {
        $request = new Request('GET /thisroute?parameter=thisvalue');
        $response = Settings::getRouter()->resolve($request);

        $this->assertMatchesRegularExpression('/Page \'\/thisroute\' not found/', $response->getHttpContent());
        $this->assertSame(404, $response->getHttpCode());
    }
}
