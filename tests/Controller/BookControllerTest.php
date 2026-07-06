<?php

declare(strict_types=1);

namespace Tests\Controller;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\TestCase;

class BookControllerTest extends TestCase
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

        Settings::getAuthentificationService()->register(
            'Jeremy',
            'jeremy@mail.com',
            'password',
            '/upload/avatars/image.png'
        );
        Settings::getAuthentificationService()->login('jeremy@mail.com', 'password');
        Settings::getBookManager()->addBook(
            'Titredulivre1',
            'unauteur1',
            '/upload/books/image1.png',
            'une description1',
            BookStatusEnum::AVAILABLE
        );
        Settings::getBookManager()->addBook(
            'Titredulivre2',
            'unauteur2',
            '/upload/books/image2.png',
            'une description2',
            BookStatusEnum::AVAILABLE
        );
        Settings::getBookManager()->addBook(
            'Titredulivre3',
            'unauteur3',
            '/upload/books/image3.png',
            'une description3',
            BookStatusEnum::AVAILABLE
        );
        Settings::getBookManager()->addBook(
            'Titredulivre4',
            'unauteur4',
            '/upload/books/image4.png',
            'une description4',
            BookStatusEnum::AVAILABLE
        );
        Settings::getBookManager()->addBook(
            'Titredulivre5',
            'unauteur5',
            '/upload/books/image5.png',
            'une description5',
            BookStatusEnum::AVAILABLE
        );
        Settings::getBookManager()->addBook(
            'Titredulivre6',
            'unauteur6',
            '/upload/books/image6.png',
            'une description6',
            BookStatusEnum::AVAILABLE
        );
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

    public function testshowBooks()
    {
        $this->assertMatchesRegularExpression(
            '/Titredulivre6/',
            Settings::getBookController()->showBooks()
        );
    }

    public function testBookDetail()
    {
        $book6 = Settings::getBookRepository()->findOneByTitle('Titredulivre6');
        $this->assertMatchesRegularExpression(
            '/Titredulivre6/',
            Settings::getBookController()->showBookDetail($book6->getId())
        );
    }

    public function testBookEdit()
    {
        $book6 = Settings::getBookRepository()->findOneByTitle('Titredulivre6');
        $this->assertMatchesRegularExpression(
            '/Edit Titredulivre6/',
            Settings::getBookController()->showBookEdit($book6->getId())
        );
    }
}
