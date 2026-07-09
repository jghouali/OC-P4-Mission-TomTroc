<?php

declare(strict_types=1);

namespace Tests\Controller;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MemberControllerTest extends TestCase
{
    private MemberEntity $member1;
    private MemberEntity $member2;
    private BookEntity $book1;
    private BookEntity $book2;

    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();

        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    public function setUp(): void
    {
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();

        $this->member1 = Settings::getAuthentificationService()->register(
            'Jeremy',
            'jeremy@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );

        $this->book1 = Settings::getBookRepository()->insert(
            new BookEntity(
                'Titre du livre de member1',
                'Auteur du livre de member1',
                '/upload/books/image-book-member1.png',
                'description du livre de member1',
                BookStatusEnum::AVAILABLE,
                $this->member1
            )
        );

        $this->member2 = Settings::getAuthentificationService()->register(
            'Jack',
            'jack@mail.com',
            'P@ssword2026',
            '/upload/avatars/image2.png'
        );

        $this->book2 = Settings::getBookRepository()->insert(
            new BookEntity(
                'Titre du livre de member2',
                'Auteur du livre de member2',
                '/upload/books/image-book-member2.png',
                'description du livre de member2',
                BookStatusEnum::AVAILABLE,
                $this->member2
            )
        );

        Settings::getAuthentificationService()->login(
            $this->member1->getEmail(),
            'P@ssword2026'
        );
    }

    public function tearDown(): void
    {
        Settings::getAuthentificationService()->logout();

        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    public static function tearDownAfterClass(): void
    {
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    #[TestDox('showRegister() when not logged return register form')]
    public function testShowRegisterNotLogged(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $result = Settings::getMemberController()->showRegister();

        $this->assertMatchesRegularExpression(
            '/\<title\>Inscription\<\/title\>/',
            $result
        );

        $this->assertMatchesRegularExpression(
            '/form action="\/register" method="post"/',
            $result
        );
    }

    #[TestDox('showRegister() when already logged return redirect to /my-profile')]
    public function testShowRegisterLogged(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->showRegister();

        $this->assertSame(
            302,
            $result->getHttpCode()
        );
        $this->assertSame(
            'Already registered in',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/my-profile/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('showLogin() when not logged show login form')]
    public function testShowLoginLogged(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->assertMatchesRegularExpression(
            '/<title>Connexion<\/title>/',
            Settings::getMemberController()->showLogin()
        );
    }

    #[TestDox('showLogin() when already logged redirect to /my-profile')]
    public function testShowLoginNotLogged(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->showLogin();

        $this->assertSame(
            302,
            $result->getHttpCode()
        );
        $this->assertSame(
            'Already Logged in',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/my-profile/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('showProfile() when not logged show profile')]
    public function testShowProfileNotLogged(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $result = Settings::getMemberController()->showProfile($this->member2->getId());

        // User Informations
        $this->assertMatchesRegularExpression(
            '/' . $this->member2->getUserName() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $this->member2->getAvatarPath()) . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/1 livre/',
            $result
        );

        // Books Informations
        $this->assertMatchesRegularExpression(
            '/' . $this->book2->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book2->getAuthor() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $this->book2->getImagePath()) . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book2->getDescription() . '/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('now'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $this->assertMatchesRegularExpression(
            '/Membre depuis aujourd\'hui/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('yesterday'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 jour/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('4 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 4 jours/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('10 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 10 jours/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('40 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 mois/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('100 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 3 mois/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('400 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 an/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('800 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 2 ans/',
            $result
        );

        // Add a 2nd book
        $book3 = Settings::getBookRepository()->insert(
            new BookEntity(
                'Titre2Livre',
                'Auteur2Livre',
                '/upload/books/image2Livre.png',
                'description2Livre',
                BookStatusEnum::NOTAVAILABLE,
                $this->member2
            )
        );
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        // Book2 Informations
        $this->assertMatchesRegularExpression(
            '/' . $book3->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $book3->getAuthor() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $book3->getDescription() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $book3->getImagePath()) . '/',
            $result
        );
    }

    #[TestDox('showProfile() when logged show profile with Username, AvatarPath,' .
        ' books count and book owned informations')]
    public function testShowProfileLogged(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->showProfile($this->member2->getId());

        // User Informations
        $this->assertMatchesRegularExpression(
            '/' . $this->member2->getUserName() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $this->member2->getAvatarPath()) . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/1 livre/',
            $result
        );

        // Books Informations
        $this->assertMatchesRegularExpression(
            '/' . $this->book2->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book2->getAuthor() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $this->book2->getImagePath()) . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book2->getDescription() . '/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('now'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $this->assertMatchesRegularExpression(
            '/Membre depuis aujourd\'hui/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('yesterday'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 jour/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('4 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 4 jours/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('10 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 10 jours/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('40 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 mois/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('100 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 3 mois/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('400 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 an/',
            $result
        );

        $this->member2->setCreatedAt(Locales::getLocalDateTime('800 days ago'));
        Settings::getMemberRepository()->update($this->member2->getId(), $this->member2);
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 2 ans/',
            $result
        );

        // Add a 2nd book
        $book3 = Settings::getBookRepository()->insert(
            new BookEntity(
                'Titre2Livre',
                'Auteur2Livre',
                '/upload/books/image2Livre.png',
                'description2Livre',
                BookStatusEnum::NOTAVAILABLE,
                $this->member2
            )
        );
        $result = Settings::getMemberController()->showProfile($this->member2->getId());
        // Book2 Informations
        $this->assertMatchesRegularExpression(
            '/' . $book3->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $book3->getAuthor() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $book3->getDescription() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $book3->getImagePath()) . '/',
            $result
        );
    }

    #[TestDox('showMyProfile() not logged throw RuntimeException')]
    public function testShowMyProfileNotLogged(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Not Logged');

        Settings::getMemberController()->showMyProfile();
    }

    #[TestDox('showMyProfile() logged show My profile with Username, AvatarPath,' .
        ' books count and book owned informations')]
    public function testShowMyProfileLogged(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->showMyProfile();

        // User Informations
        $this->assertMatchesRegularExpression(
            '/' . $this->member1->getUserName() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->member1->getEmail() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $this->member1->getAvatarPath()) . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/1 livre/',
            $result
        );

        // Books Informations
        $this->assertMatchesRegularExpression(
            '/' . $this->book1->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book1->getAuthor() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $this->book1->getImagePath()) . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book1->getDescription() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/disponible/',
            $result
        );
    }

    #[TestDox('register() return redirect to /my-profile when successfull')]
    public function testRegister(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $username = 'Jean';
        $email = 'jean@mail.com';
        $password = 'P@ssword2026';

        $result = Settings::getMemberController()->register($username, $email, $password);

        $this->assertSame(
            303,
            $result->getHttpCode()
        );
        $this->assertSame(
            'Success',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/my-profile/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('register() with no username throw RuntimeException')]
    public function testRegisterNoUsername(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $username = '';
        $email = 'jean@mail.com';
        $password = 'P@ssword2026';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid username : username must only contain 50 readable characters');

        Settings::getMemberController()->register($username, $email, $password);
    }

    #[TestDox('register() with no email throw RuntimeException')]
    public function testRegisterNoEmail(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $username = 'Jean';
        $email = '';
        $password = 'P@ssword2026';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid email : email is not a valid email');

        Settings::getMemberController()->register($username, $email, $password);
    }

    #[TestDox('register() with no password throw RuntimeException')]
    public function testRegisterNoPassword(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $username = 'Jean';
        $email = 'jean@mail.com';
        $password = '';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid password : password must contain between 12 and 72' .
            ' character and at least one [a-z], one [0-9], one [!@#$%^&*()_\-+=.?]');

        Settings::getMemberController()->register($username, $email, $password);
    }

    #[TestDox('register() with invalid username throw RuntimeException')]
    public function testRegisterInvalidUsername(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $username = '￿';
        $email = 'jean@mail.com';
        $password = 'P@ssword2026';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid username : username must only contain 50 readable characters');

        Settings::getMemberController()->register($username, $email, $password);
    }

    #[TestDox('register() with invalid email throw RuntimeException')]
    public function testRegisterInvalidEmail(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $username = 'Jean';
        $email = 'mailmailmail';
        $password = 'P@ssword2026';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid email : email is not a valid email');

        Settings::getMemberController()->register($username, $email, $password);
    }

    #[TestDox('register() with invalid password throw RuntimeException')]
    public function testRegisterInvalidPassword(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $username = 'Jean';
        $email = 'jean@mail.com';
        $password = 'P@ssw￿ord2026';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid password : password must contain between 12 and 72 character' .
            ' and at least one [a-z], one [0-9], one [!@#$%^&*()_\-+=.?]');

        Settings::getMemberController()->register($username, $email, $password);
    }

    #[TestDox('login() with invalid email send error')]
    public function testLoginInvalidEmail(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $email = 'mailmailmail';
        $password = '￿';

        $result = Settings::getMemberController()->login($email, $password);

        $this->assertMatchesRegularExpression('/Login error : check your credential/', $result);
    }

    #[TestDox('login() with invalid password send error')]
    public function testLoginInvalidPassword(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $email = 'jean@mail.com';
        $password = '￿';

        $result = Settings::getMemberController()->login($email, $password);

        $this->assertMatchesRegularExpression('/Login error : check your credential/', $result);
    }

    #[TestDox('logout() when logged redirect to /')]
    public function testLoggedOut(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->logout();

        $this->assertSame(
            303,
            $result->getHttpCode()
        );
        $this->assertSame(
            'Success',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/\//',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('logout() when not logged redirect to /login')]
    public function testLoggedOutNotLogged(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $result = Settings::getMemberController()->logout();

        $this->assertSame(
            302,
            $result->getHttpCode()
        );
        $this->assertSame(
            'Not Logged',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/\/login/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('modifyMyProfile() when logged redirect to /my-profile when successfull')]
    public function testModifyMyProfileLogged(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->modifyMyProfile(
            'newemail@mail.com',
            'newP@ssword2026',
            'newusername',
            ['error' => 4]
        );

        $this->assertSame(
            $result->getHttpCode(),
            303
        );
        $this->assertSame(
            'Update successfully',
            $result->getHttpContent()
        );
        $this->assertTrue(
            $result->getHttpHeader()['Location:'] === '/my-profile'
        );
    }

    #[TestDox('modifyMyProfile() when logged with just email redirect to /my-profile when successfull')]
    public function testModifyMyProfileLoggedJustEmail(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->modifyMyProfile(
            'newemail@mail.com',
            '',
            '',
            ['error' => 4]
        );

        $this->assertSame(
            $result->getHttpCode(),
            303
        );
        $this->assertSame(
            'Update successfully',
            $result->getHttpContent()
        );
        $this->assertTrue(
            $result->getHttpHeader()['Location:'] === '/my-profile'
        );
    }

    #[TestDox('modifyMyProfile() when logged with just password redirect to /my-profile when successfull')]
    public function testModifyMyProfileLoggedJustPassword(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->modifyMyProfile(
            '',
            'Newpassword@2026',
            '',
            ['error' => 4]
        );

        $this->assertSame(
            $result->getHttpCode(),
            303
        );
        $this->assertSame(
            'Update successfully',
            $result->getHttpContent()
        );
        $this->assertTrue(
            $result->getHttpHeader()['Location:'] === '/my-profile'
        );
    }

    #[TestDox('modifyMyProfile() when logged with just username redirect to /my-profile when successfull')]
    public function testModifyMyProfileLoggedJustUsername(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMemberController()->modifyMyProfile(
            '',
            '',
            'NewUsername',
            ['error' => 4]
        );

        $this->assertSame(
            $result->getHttpCode(),
            303
        );
        $this->assertSame(
            'Update successfully',
            $result->getHttpContent()
        );
        $this->assertTrue(
            $result->getHttpHeader()['Location:'] === '/my-profile'
        );
    }

    #[TestDox('modifyMyProfile() when logged with invalid email throw RunTimeException')]
    public function testModifyMyProfileLoggedInvalidEmail(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid email : email is not a valid email');

        Settings::getMemberController()->modifyMyProfile(
            'newemail￿@mail.com',
            '',
            '',
            ['error' => 4]
        );
    }

    #[TestDox('modifyMyProfile() when logged with invalid password throw RunTimeException')]
    public function testModifyMyProfileLoggedInvalidPassword(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid password : password must only contain 50 readable characters');

        Settings::getMemberController()->modifyMyProfile(
            '',
            'Newpassword￿@2026',
            '',
            ['error' => 4]
        );
    }

    #[TestDox('modifyMyProfile() when logged with invalid username throw RunTimeException')]
    public function testModifyMyProfileLoggedInvalidUsername(): void
    {
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid username : username must only contain 50 readable characters');

        Settings::getMemberController()->modifyMyProfile(
            '',
            '',
            'New￿Username',
            ['error' => 4]
        );
    }

    #[TestDox('modifyMyProfile() when not logged throw RuntimeException')]
    public function testModifyMyProfileNotLogged(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Not Logged');

        Settings::getMemberController()->modifyMyProfile(
            'newemail@mail.com',
            'newP@ssword2026',
            'newusername',
            ['error' => 4]
        );
    }
}
