<?php

declare(strict_types=1);

namespace Tests\Core\Service;

use Green\TomTroc\Core\Settings\Settings;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class AuthentificationServiceTest extends TestCase
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

    #[TestDox('isLoggedIn() return false when not logged in')]
    public function testIsLoggedInWhenNotLoggedIn()
    {
        $authentificationService = Settings::getAuthentificationService();
        $this->assertFalse($authentificationService->isLoggedIn());
    }

    #[TestDox('isLoggedIn() return true when logged in')]
    public function testIsLoggedInWhenLoggedIn()
    {
        $member = Settings::getAuthentificationService()->register(
            'John Doe',
            'john.doe@mail.com',
            'password',
            '/upload/avatars/image.png'
        );
        Settings::getAuthentificationService()->login($member->getEmail(), 'password');
        $this->assertTrue(Settings::getAuthentificationService()->isLoggedIn());
    }

    #[TestDox('getCurrentLoggedMember() return MemberEntity when logged in')]
    public function testGetCurrentLoggedMemberWhenLoggedIn()
    {
        $this->assertFalse(isset($_SESSION['id']));
        $member = Settings::getAuthentificationService()->register(
            'John Doe',
            'john.doe@mail.com',
            'password',
            '/upload/avatars/image.png'
        );
        Settings::getAuthentificationService()->login($member->getEmail(), 'password');
        $this->assertSame(
            'Green\TomTroc\Entity\MemberEntity',
            Settings::getAuthentificationService()->getCurrentLoggedMember()::class
        );
    }

    #[TestDox('getCurrentLoggedMember() return null when not logged in')]
    public function testGetCurrentLoggedMemberWhenNotLoggedIn()
    {
        $member = Settings::getAuthentificationService()->register(
            'John Doe',
            'john.doe@mail.com',
            'password',
            '/upload/avatars/image.png'
        );
        $this->assertSame(
            null,
            Settings::getAuthentificationService()->getCurrentLoggedMember()
        );
    }
}
