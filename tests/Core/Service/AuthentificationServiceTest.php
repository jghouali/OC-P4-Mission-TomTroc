<?php

declare(strict_types=1);

namespace Tests\Core\Service;

use Green\TomTroc\Core\Service\ValidatorService;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Enum\ValidatorEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
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
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());
    }

    #[TestDox('isLoggedIn() return true when logged in')]
    public function testIsLoggedInWhenLoggedIn()
    {
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $member = Settings::getAuthentificationService()->register(
            'John Doe',
            'john.doe@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
        Settings::getAuthentificationService()->login($member->getEmail(), 'P@ssword2026');

        $this->assertTrue(Settings::getAuthentificationService()->isLoggedIn());
    }

    #[TestDox('getCurrentLoggedMember() return MemberEntity when logged in')]
    public function testGetCurrentLoggedMemberWhenLoggedIn()
    {
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());
        $member = Settings::getAuthentificationService()->register(
            'John Doe',
            'john.doe@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
        Settings::getAuthentificationService()->login($member->getEmail(), 'P@ssword2026');

        $this->assertSame(
            'John Doe',
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );
    }

    #[TestDox('getCurrentLoggedMember() return null when not logged in')]
    public function testGetCurrentLoggedMemberWhenNotLoggedIn()
    {
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());
        $this->assertSame(
            null,
            Settings::getAuthentificationService()->getCurrentLoggedMember()
        );
    }

    #[TestDox('generatePasswordHash() return a bcryptHash when password is valid')]
    public function testGeneratePasswordHash()
    {
        $passwordHash = Settings::getAuthentificationService()->generatePasswordHash('P@ssword2026');

        $this->assertTrue(
            ValidatorService::validateField(
                'passwordHash',
                $passwordHash,
                ValidatorEnum::bcryptHash
            ) === $passwordHash
        );
    }

    #[TestDox('generatePasswordHash() throw RuntimeException when password is invalid')]
    public function testGeneratePasswordHashInvalidPassword()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains(
            'password must contain between 12 and 72 character and at least one [a-z]'
        );

        Settings::getAuthentificationService()->generatePasswordHash('password');
    }

    #[TestDox('register() return a valid MemberEntity when succesfull')]
    public function testRegister()
    {
        $result = Settings::getAuthentificationService()->register(
            'John Doe',
            'john.doe@mail.com',
            'P@ssword2026!',
            '/upload/avatars/image.png'
        );
        $this->assertSame(
            'Green\\TomTroc\\Entity\\MemberEntity',
            $result::class
        );
        $this->assertSame(
            'John Doe',
            $result->getUserName()
        );
    }

    #[TestDox('register() throw RuntimeException')]
    #[TestWith(['John࿿Doe'])]
    #[TestWith([''])]
    public function testRegisterThrowExceptionUsername(string $username)
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains(
            'Invalid username : username must only contain 50 readable characters'
        );

        Settings::getAuthentificationService()->register(
            $username,
            'john.doe@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
    }

    #[TestDox('register() throw RuntimeException')]
    #[TestWith(['john.doe࿿mail.com'])]
    #[TestWith([''])]
    public function testRegisterThrowExceptionEmail(string $email)
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains(
            'Invalid email : email is not a valid email'
        );

        Settings::getAuthentificationService()->register(
            'John Doe',
            $email,
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
    }

    #[TestDox('register() throw RuntimeException')]
    #[TestWith(['P@ssword࿿2026'])]
    #[TestWith([''])]
    public function testRegisterThrowExceptionPassword(string $password)
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains(
            'Invalid password : password must contain between 12 and 72 character and at least one'
        );

        Settings::getAuthentificationService()->register(
            'John Doe',
            'john.doe@mail.com',
            $password,
            '/upload/avatars/image.png'
        );
    }

    #[TestDox('register() throw RuntimeException')]
    #[TestWith(['/../../../etc/password'])]
    #[TestWith(['/upload/avatars/image࿿.png'])]
    #[TestWith([''])]
    public function testRegisterInvalidAvatarPath(string $avatarPath): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains(
            'Invalid avatarPath : avatarPath must be stored in /upload/avatars/,'
        );

        Settings::getAuthentificationService()->register(
            'John Doe',
            'john.doe@mail.com',
            'P@ssword2026',
            $avatarPath
        );
    }

    #[TestDox('register() with already registered email throw RuntimeException')]
    public function testRegisterEmailAlreadyExist(): void
    {
        Settings::getAuthentificationService()->register(
            'Test1',
            'test1@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains(
            'email already registered'
        );

        Settings::getAuthentificationService()->register(
            'Test2',
            'test1@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
    }

    #[TestDox('register() with already registered username throw RuntimeException')]
    public function testRegisterUsernameAlreadyExist(): void
    {
        Settings::getAuthentificationService()->register(
            'Test1',
            'test1@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains(
            'username already registered'
        );

        Settings::getAuthentificationService()->register(
            'Test1',
            'test2@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
    }

    #[TestDox('login() with registered account return True')]
    public function testLoginValidAccount(): void
    {
        Settings::getAuthentificationService()->register(
            'Test1',
            'test1@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->assertTrue(Settings::getAuthentificationService()->login('test1@mail.com', 'P@ssword2026'));
    }

    #[TestDox('login() with non existent account return False')]
    public function testLoginNonExistentAccount(): void
    {
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->assertFalse(Settings::getAuthentificationService()->login('nonexistent@mail.com', 'P@ssword2026'));
    }

    #[TestDox('login() with Valid Account and bad password return False')]
    public function testLoginValidAccountBadPassword(): void
    {
        Settings::getAuthentificationService()->register(
            'Test1',
            'test1@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->assertFalse(Settings::getAuthentificationService()->login('test1@mail.com', 'badpassword'));
    }

    #[TestDox('logout()')]
    public function testLogout(): void
    {
        Settings::getAuthentificationService()->register(
            'Test1',
            'test1@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->assertTrue(Settings::getAuthentificationService()->login('test1@mail.com', 'P@ssword2026'));

        $this->assertTrue(Settings::getAuthentificationService()->isLoggedIn());

        $this->assertTrue(Settings::getAuthentificationService()->logout());

        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());
    }
}
