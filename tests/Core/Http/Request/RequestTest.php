<?php

declare(strict_types=1);

namespace Tests\Core\Http\Request;

use Green\TomTroc\Core\Http\Request;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $_POST = [];
    }

    public function setUp(): void
    {
        $_POST = [];
    }

    public function tearDown(): void
    {
        $_POST = [];
    }

    public static function tearDownAfterClass(): void
    {
        $_POST = [];
    }

    #[TestDox('constructor with valid request string return a valid Request')]
    public function testRequestConstructor()
    {
        // GIVEN
        // this request : GET /unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal

        // WHEN
        // instanciante a new Request
        $request = new Request('GET /unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal');

        // EXPECT
        // is a valid Request
        $this->assertSame(
            'Green\\TomTroc\\Core\\Http\\Request',
            $request::class
        );
        $this->assertSame(
            'GET',
            $request->getHttpMethod()
        );
        $this->assertSame(
            '/unePage',
            $request->getHttpLocation()
        );
        $this->assertSame(
            'unParametre=uneValeur&unAutreParametre=uneAutreVal',
            $request->getHttpParameters(false)
        );
        $this->assertSame(
            [
                'unParametre' => 'uneValeur',
                'unAutreParametre' => 'uneAutreVal',
            ],
            $request->getHttpParameters(true)
        );
    }

    #[TestDox('constructor with invalid request string throw RuntimeException')]
    public function testRequestConstructorInvalidRequest()
    {
        // GIVEN
        // this request : GET/unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Malformed Request/');

        // WHEN
        // instanciante a new Request
        new Request('GET/unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal');
    }

    #[TestDox('constructor with invalid method in request string throw RuntimeException')]
    public function testRequestConstructorInvalidMethodInRequest()
    {
        // GIVEN
        // this request : ERROR /unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Method.*not allowed/');

        // WHEN
        // instanciante a new Request
        new Request('ERROR /unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal');
    }

    #[TestDox('constructor with malformed request throw RuntimeException')]
    public function testRequestConstructorMalformedRequestInRequest()
    {
        // GIVEN
        // this request : GET ?unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Malformed Request/');

        // WHEN
        // instanciante a new Request
        new Request('GET ?unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal');
    }

    #[TestDox('constructor with malformed location throw RuntimeException')]
    public function testRequestConstructorInvalidLocationInRequest()
    {
        // GIVEN
        // this request : GET !unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Http Location doesnt start with \'\/\'/');

        // WHEN
        // instanciante a new Request
        new Request('GET !unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal');
    }

    #[TestDox('constructor with malformed parameters throw RuntimeException')]
    public function testRequestConstructorInvalidParametersInRequest()
    {
        // GIVEN
        // this request : GET /unePage?unParametre=

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Malformed Parameters/');

        // WHEN
        // instanciante a new Request
        new Request('GET /unePage?a');
    }

    #[TestDox('getters() return valid data')]
    public function testRequestGetters()
    {
        // GIVEN
        // this request : GET /unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal

        // WHEN
        // instanciante a new Request
        $request = new Request('GET /unePage?unParametre=uneValeur&unAutreParametre=uneAutreVal');

        // EXPECT
        // is a valid Request
        $this->assertSame(
            'Green\\TomTroc\\Core\\Http\\Request',
            $request::class
        );
        $this->assertSame(
            'GET',
            $request->getHttpMethod()
        );
        $this->assertSame(
            '/unePage',
            $request->getHttpLocation()
        );
        $this->assertSame(
            'unParametre=uneValeur&unAutreParametre=uneAutreVal',
            $request->getHttpParameters(false)
        );
        $this->assertSame(
            [
                'unParametre' => 'uneValeur',
                'unAutreParametre' => 'uneAutreVal',
            ],
            $request->getHttpParameters(true)
        );
    }

    #[TestDox('constructor with POST method and valid post data')]
    public function testRequestConstructorPostWithPostDataInRequest()
    {
        // GIVEN
        // this request : POST /unePage
        $_POST = [
            'unParametre' => 'uneValeur',
            'unAutreParametre' => 'uneAutreValeur',
        ];

        // WHEN
        // instanciante a new Request
        $request = new Request('POST /unePage');

        // EXPECT
        // is a valid Request
        $this->assertSame(
            'Green\\TomTroc\\Core\\Http\\Request',
            $request::class
        );
        $this->assertSame(
            'POST',
            $request->getHttpMethod()
        );
        $this->assertSame(
            '/unePage',
            $request->getHttpLocation()
        );
        $this->assertSame(
            'unParametre=uneValeur&unAutreParametre=uneAutreValeur',
            $request->getHttpParameters(false)
        );
        $this->assertSame(
            [
                'unParametre' => 'uneValeur',
                'unAutreParametre' => 'uneAutreValeur',
            ],
            $request->getHttpParameters(true)
        );
    }

    #[TestDox('constructor with POST method and no post data throw RuntimeException')]
    public function testRequestConstructorPostWithNoPostDataInRequest()
    {
        // GIVEN
        // this request : POST /unePage

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Can\'t process \'POST\' request whitout \'POST\' data./');

        // WHEN
        // instanciante a new Request
        new Request('POST /unePage');
    }
}
