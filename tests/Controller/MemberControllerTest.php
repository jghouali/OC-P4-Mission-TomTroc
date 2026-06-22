<?php

declare(strict_types=1);

namespace Tests\Controller;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\TestCase;

class MemberControllerTest extends TestCase
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

    public function testShowRegister(): void
    {
        $this->assertMatchesRegularExpression(
            '/<title>Inscription<\/title>/',
            Settings::getMemberController()->showRegister()
        );
    }

    public function testRegister(): void
    {
        $username = 'Jeremy';
        $email = 'jeremy@mail.com';
        $password = 'password';
        $this->assertMatchesRegularExpression(
            '/<title>Inscription<\/title>/',
            Settings::getMemberController()->register($username, $email, $password)
        );
    }

    public function testShowLogin(): void
    {
        $this->assertMatchesRegularExpression(
            '/<title>Connexion<\/title>/',
            Settings::getMemberController()->showLogin()
        );
    }

    public function testShowProfile(): void
    {
        $member = Settings::getAuthentificationService()->register(
            'Jacques',
            'jacques@mail.com',
            'password',
            '/upload/avatars/jacques.png'
        );
        $member2 = Settings::getAuthentificationService()->register(
            'Jeremy',
            'jeremy@mail.com',
            'password',
            '/upload/avatars/image.png'
        );

        Settings::getAuthentificationService()->login('jacques@mail.com', 'password');
        Settings::getBookRepository()->insert(
            new BookEntity(
                'Titre du livre',
                'Auteur',
                '/upload/books/image.png',
                'description',
                BookStatusEnum::AVAILABLE,
                $member2
            )
        );

        $result = Settings::getMemberController()->showProfile($member2->getId());

        // User Informations
        $this->assertMatchesRegularExpression(
            '/Jeremy/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/\/upload\/avatars\/image.png/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/1 livre/',
            $result
        );

        // Books Informations
        $this->assertMatchesRegularExpression(
            '/Titre du livre/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/Auteur/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/\/upload\/books\/image.png/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/description/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/AVAILABLE/',
            $result
        );

        $member2->setCreatedAt(Locales::getLocalDateTime('now'));
        Settings::getMemberRepository()->update($member2->getId(), $member2);
        $this->assertMatchesRegularExpression(
            '/Membre depuis aujourd\'hui/',
            $result
        );

        $member2->setCreatedAt(Locales::getLocalDateTime('yesterday'));
        Settings::getMemberRepository()->update($member2->getId(), $member2);
        $result = Settings::getMemberController()->showProfile($member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 jour/',
            $result
        );

        $member2->setCreatedAt(Locales::getLocalDateTime('4 days ago'));
        Settings::getMemberRepository()->update($member2->getId(), $member2);
        $result = Settings::getMemberController()->showProfile($member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 4 jours/',
            $result
        );

        $member2->setCreatedAt(Locales::getLocalDateTime('10 days ago'));
        Settings::getMemberRepository()->update($member2->getId(), $member2);
        $result = Settings::getMemberController()->showProfile($member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 10 jours/',
            $result
        );

        $member2->setCreatedAt(Locales::getLocalDateTime('40 days ago'));
        Settings::getMemberRepository()->update($member2->getId(), $member2);
        $result = Settings::getMemberController()->showProfile($member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 mois/',
            $result
        );

        $member2->setCreatedAt(Locales::getLocalDateTime('100 days ago'));
        Settings::getMemberRepository()->update($member2->getId(), $member2);
        $result = Settings::getMemberController()->showProfile($member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 3 mois/',
            $result
        );

        $member2->setCreatedAt(Locales::getLocalDateTime('400 days ago'));
        Settings::getMemberRepository()->update($member2->getId(), $member2);
        $result = Settings::getMemberController()->showProfile($member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 1 an/',
            $result
        );

        $member2->setCreatedAt(Locales::getLocalDateTime('800 days ago'));
        Settings::getMemberRepository()->update($member2->getId(), $member2);
        $result = Settings::getMemberController()->showProfile($member2->getId());
        $this->assertMatchesRegularExpression(
            '/Membre depuis 2 ans/',
            $result
        );

        // Add a 2nd book
        Settings::getBookRepository()->insert(
            new BookEntity(
                'Titre2Livre',
                'Auteur2Livre',
                '/upload/books/image2Livre.png',
                'description2Livre',
                BookStatusEnum::NOTAVAILABLE,
                $member2
            )
        );
        $result = Settings::getMemberController()->showProfile($member2->getId());
        // Book2 Informations
        $this->assertMatchesRegularExpression(
            '/Titre2Livre/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/Auteur2Livre/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/\/upload\/books\/image2Livre.png/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/description2Livre/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/NOT-AVAILABLE/',
            $result
        );
    }
}
