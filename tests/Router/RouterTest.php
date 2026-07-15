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

    #[TestDox('register() can set a callable as route')]
    public function testRegister(): void
    {
        // GIVEN
        // have a function
        $testCallable = function ($rememberme) {
            ob_start();
            echo "rememberme = $rememberme";
            $data = ob_get_clean();
            return "LoginForm with : $data";
        };

        // WHEN
        // register()
        Settings::getRouter()->register('GET', '/login', $testCallable(...));
        $routes = Settings::getRouter()->getRoutes();

        // EXPECT
        // found a closure on that route
        $this->assertSame('Closure', $routes['/login']['GET']::class);
    }

    #[TestDox('register() with a bad method throw RuntimeException')]
    public function testRegisterWithBadMethodThrowRuntimeException(): void
    {
        // GIVEN
        // have a function
        $testCallable = function ($rememberme) {
            ob_start();
            echo "rememberme = $rememberme";
            $data = ob_get_clean();
            return "LoginForm with : $data";
        };

        // EXPECT
        // throw a RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('Trying to register an invalid Http Method');

        // WHEN
        // register a bad method
        Settings::getRouter()->register('BAD', '/login', $testCallable(...));
    }

    #[TestDox('register() with a bad location throw RuntimeException')]
    public function testRegisterWithBadLocationThrowRuntimeException(): void
    {
        // GIVEN
        // have a function
        $testCallable = function ($rememberme) {
            ob_start();
            echo "rememberme = $rememberme";
            $data = ob_get_clean();
            return "LoginForm with : $data";
        };

        // EXPECT
        // throw a RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('Trying to register an invalid Http Location');

        // WHEN
        // register a bad location
        Settings::getRouter()->register('GET', '/login/%', $testCallable(...));
    }

    #[TestDox('resolve() on an existing route return the result of the callable')]
    public function testResolveWithExistingRouteReturnAHttp200ResponseObject(): void
    {
        // GIVEN
        // have a function already registered
        $testCallable = function ($rememberme) {
            ob_start();
            echo "rememberme = $rememberme";
            $data = ob_get_clean();
            return "LoginForm with : $data";
        };
        Settings::getRouter()->register('GET', '/login', $testCallable(...));
        // and a request on that route
        $request = new Request('GET /login?rememberme=1');

        // WHEN
        // we resolve that request
        $response = Settings::getRouter()->resolve($request);

        // EXPECT
        // We got a vald response representing the result of that function
        $this->assertSame('Green\TomTroc\Core\Http\Response', $response::class);
        $this->assertSame(200, $response->getHttpCode());
        $this->assertMatchesRegularExpression('/LoginForm with : rememberme = 1/', $response->getHttpContent());
    }

    #[TestDox('resolve() on an inexisting route return pageNotFoundContent()')]
    public function testPageNotFoundContent(): void
    {
        // GIVEN
        // have a request on an inexisting location
        $request = new Request('GET /thisroute?parameter=thisvalue');

        // WHEN
        // we resolve that request
        $response = Settings::getRouter()->resolve($request);

        // EXPECT
        // return a page not found with this location
        $this->assertMatchesRegularExpression('/Page \/thisroute not found/', $response->getHttpContent());
        $this->assertSame(404, $response->getHttpCode());
    }
}
