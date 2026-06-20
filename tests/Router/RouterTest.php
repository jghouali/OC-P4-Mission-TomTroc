<?php

declare(strict_types=1);

namespace Tests\Router;

use Green\TomTroc\Core\Http\Request;
use Green\TomTroc\Core\Settings\Settings;
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

    public function testResolveReturnAResponseObject(): void
    {
        $requestUri = '/login?rememberme=1';
        $request = new Request($requestUri);

        $this->assertSame('Green\TomTroc\Core\Http\Response', Settings::getRouter()->resolve($request)::class);
    }

    public function testRegisterCanSetACallableAsRoute(): void
    {
        $testCallable = function ($a) {
            ob_start();
            var_dump($a);
            $data = ob_get_clean();
            return "This Content $data";
        };
        Settings::getRouter()->register('/', $testCallable);

        $request = new Request('/?thisparameter=thisvalue');
        $response = Settings::getRouter()->resolve($request);

        $this->assertMatchesRegularExpression('/This Content/', $response->getHttpContent());
        $this->assertMatchesRegularExpression('/thisparameter/', $response->getHttpContent());
        $this->assertMatchesRegularExpression('/thisvalue/', $response->getHttpContent());
    }

    public function testPageNotFoundContent(): void
    {
        $request = new Request('/thisroute?parameter=thisvalue');
        $response = Settings::getRouter()->resolve($request);

        $this->assertMatchesRegularExpression('/\/thisroute not found/', $response->getHttpContent());
    }
}
