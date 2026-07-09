<?php

declare(strict_types=1);

namespace Tests\Controller;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['avatarPath']);
    }

    public function setUp(): void
    {
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['avatarPath']);
    }

    public function tearDown(): void
    {
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['avatarPath']);
    }

    public static function tearDownAfterClass(): void
    {
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['avatarPath']);
    }

    public function testshowHomePage()
    {
        Settings::getAuthentificationService()->register(
            'Jeremy',
            'jeremy@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
        Settings::getAuthentificationService()->login('jeremy@mail.com', 'P@ssword2026');
        Settings::getBookManager()->addBook(
            'Titredulivre',
            'unauteur',
            '/upload/books/image.png',
            'une description',
            BookStatusEnum::AVAILABLE
        );

        $this->assertMatchesRegularExpression(
            '/<title>Tomtroc Accueil<\/title>/',
            Settings::getHomeController()->showHomePage()
        );
        $this->assertMatchesRegularExpression(
            '/Rejoignez nos lecteurs passionnés/',
            Settings::getHomeController()->showHomePage()
        );
        $this->assertMatchesRegularExpression(
            '/Titredulivre/',
            Settings::getHomeController()->showHomePage()
        );
    }
}
