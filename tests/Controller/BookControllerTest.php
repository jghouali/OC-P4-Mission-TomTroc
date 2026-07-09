<?php

declare(strict_types=1);

namespace Tests\Controller;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\BookEntityTest;
use Tests\Entity\MemberEntityTest;

class BookControllerTest extends TestCase
{
    private MemberEntity $member1;
    private MemberEntity $member2;
    private MemberEntity $member3;
    private BookEntity $book1;
    private BookEntity $book2;
    private BookEntity $book3;
    private BookEntity $book4;
    private BookEntity $book5;
    private BookEntity $book6;

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

        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2026');

        $this->book1 = Settings::getBookManager()->addBook(
            'Titredulivre1',
            'unauteur1',
            '/upload/books/image1.png',
            'une description1',
            BookStatusEnum::AVAILABLE
        );
        $this->book2 = Settings::getBookManager()->addBook(
            'Titredulivre2',
            'unauteur2',
            '/upload/books/image2.png',
            'une description2',
            BookStatusEnum::AVAILABLE
        );
        $this->book3 = Settings::getBookManager()->addBook(
            'Titredulivre3',
            'unauteur3',
            '/upload/books/image3.png',
            'une description3',
            BookStatusEnum::AVAILABLE
        );
        $this->book4 = Settings::getBookManager()->addBook(
            'Titredulivre4',
            'unauteur4',
            '/upload/books/image4.png',
            'une description4',
            BookStatusEnum::AVAILABLE
        );
        $this->book5 = Settings::getBookManager()->addBook(
            'Titredulivre5',
            'unauteur5',
            '/upload/books/image5.png',
            'une description5',
            BookStatusEnum::AVAILABLE
        );
        $this->book6 = Settings::getBookManager()->addBook(
            'Titredulivre6',
            'unauteur6',
            '/upload/books/image6.png',
            'une description6',
            BookStatusEnum::AVAILABLE
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

    #[TestDox('showBooks() show all books in db')]
    public function testshowBooks()
    {

        $result = Settings::getBookController()->showBooks();
        $this->assertMatchesRegularExpression(
            '/' . $this->book1->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book2->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book3->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book4->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book5->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book6->getTitle() . '/',
            $result
        );
    }

    #[TestDox('showBookDetail() with existing id show Title, Author, Decription and ImagePath')]
    public function testShowBookDetail()
    {
        $result = Settings::getBookController()->showBookDetail($this->book6->getId());

        $this->assertMatchesRegularExpression(
            '/' . $this->book6->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book6->getAuthor() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book6->getDescription() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $this->book6->getImagePath()) . '/',
            $result
        );
    }

    #[TestDox('showBookDetail("16") with inexisting id throw RuntimeException')]
    public function testShowBookDetailNonExistingId()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/bookId 16 does not/');

        Settings::getBookController()->showBookDetail('16');
    }

    #[TestDox('showBookDetail("az") with invalid id throw RuntimeException')]
    public function testBookDetailInvalidId()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Invalid bookId/');

        Settings::getBookController()->showBookDetail('az');
    }

    #[TestDox('showBookEdit() with valid id and logged show edit form' .
        ' with Title, Author, Decription and ImagePath on it')]
    public function testShowBookEdit()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $result = Settings::getBookController()->showBookEdit($this->book6->getId());

        $this->assertMatchesRegularExpression(
            '/form action="\/book-edit" method="POST"/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book6->getTitle() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book6->getAuthor() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . $this->book6->getDescription() . '/',
            $result
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_replace('/\//', '\\/', $this->book6->getImagePath()) . '/',
            $result
        );
    }

    #[TestDox('showBookEdit("16") with inexisting id and logged throw RuntimeException')]
    public function testShowBookEditNonExistingId()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/bookId 16 does not/');

        Settings::getBookController()->showBookEdit('16');
    }

    #[TestDox('showBookEdit("az") with invalid id and logged throw RuntimeException')]
    public function testBookEditInvalidId()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Invalid bookId/');

        Settings::getBookController()->showBookEdit('az');
    }

    #[TestDox('showBookEdit($book6->getId()) with valid id and not logged throw RuntimeException')]
    public function testShowBookEditNotLogged()
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Not Logged/');

        Settings::getBookController()->showBookEdit($this->book6->getId());
    }

    #[TestDox('showBookEdit() not owned throw RuntimeException')]
    public function testShowBookEditNotOwned()
    {
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2 = Settings::getMemberRepository()->insert($member2);
        $book7 = BookEntityTest::instanciateValidBook();
        $book7->setFromMember($member2);
        $book7 = Settings::getBookRepository()->insert($book7);

        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/You cant edit this book/');

        Settings::getBookController()->showBookEdit($book7->getId());
    }

    #[TestDox('showBookAdd() logged show book-add form')]
    public function testShowBookAddLogged()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $this->assertMatchesRegularExpression(
            '/form.*\/book-add" method="POST"/',
            Settings::getBookController()->showBookAdd()
        );
    }

    #[TestDox('showBookAdd() not logged throw RuntimeException')]
    public function testShowBookAddNotLogged()
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Not Logged/');

        Settings::getBookController()->showBookAdd();
    }

    #[TestDox('bookUpdate() logged with no image redirect to /book-detail when successfull')]
    public function testBookUpdateLogged()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $book7 = Settings::getBookManager()->addBook(
            'Titre',
            'Auteur',
            '/upload/books/image.png',
            'Description',
            BookStatusEnum::AVAILABLE
        );

        $result = Settings::getBookController()->bookUpdate(
            (string) $book7->getId(),
            BookStatusEnum::AVAILABLE->value,
            'Titre7',
            'Auteru7',
            'Description7',
            ['error' => 4],
        );

        $this->assertSame(
            303,
            $result->getHttpCode()
        );
        $this->assertSame(
            'OK',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/book-detail\?bookId=/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('BookUpdate() just title redirect to /book-detail when successfull')]
    public function testBookUpdateTitle()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $book7 = Settings::getBookManager()->addBook(
            'Titre',
            'Auteur',
            '/upload/books/image.png',
            'Description',
            BookStatusEnum::AVAILABLE
        );

        $result = Settings::getBookController()->bookUpdate(
            (string) $book7->getId(),
            BookStatusEnum::AVAILABLE->value,
            'Titre7',
            '',
            '',
            ['error' => 4],
        );

        $this->assertSame(
            303,
            $result->getHttpCode()
        );
        $this->assertSame(
            'OK',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/book-detail\?bookId=/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('BookUpdate() just Author redirect to /book-detail when successfull')]
    public function testBookUpdateNoAuthor()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $book7 = Settings::getBookManager()->addBook(
            'Titre',
            'Auteur',
            '/upload/books/image.png',
            'Description',
            BookStatusEnum::AVAILABLE
        );

        $result = Settings::getBookController()->bookUpdate(
            (string) $book7->getId(),
            BookStatusEnum::AVAILABLE->value,
            '',
            'Auteur7',
            '',
            ['error' => 4],
        );

        $this->assertSame(
            303,
            $result->getHttpCode()
        );
        $this->assertSame(
            'OK',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/book-detail\?bookId=/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('BookUpdate() just Description redirect to /book-detail when successfull')]
    public function testBookUpdateDescription()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $book7 = Settings::getBookManager()->addBook(
            'Titre',
            'Auteur',
            '/upload/books/image.png',
            'Description',
            BookStatusEnum::AVAILABLE
        );

        $result = Settings::getBookController()->bookUpdate(
            (string) $book7->getId(),
            BookStatusEnum::AVAILABLE->value,
            '',
            '',
            'Description7',
            ['error' => 4],
        );

        $this->assertSame(
            303,
            $result->getHttpCode()
        );
        $this->assertSame(
            'OK',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/book-detail\?bookId=/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('BookUpdate() not logged throw RuntimeException')]
    public function testBookUpdateNotLogged()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $book7 = Settings::getBookManager()->addBook(
            'Titre',
            'Auteur',
            '/upload/books/image.png',
            'Description',
            BookStatusEnum::AVAILABLE
        );

        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Not Logged/');

        Settings::getBookController()->bookUpdate(
            (string) $book7->getId(),
            BookStatusEnum::AVAILABLE->value,
            '',
            '',
            'Description7',
            ['error' => 4],
        );
    }

    #[TestDox('BookUpdate() not owned throw RuntimeException')]
    public function testBookUpdateNotOwned()
    {
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2 = Settings::getMemberRepository()->insert($member2);
        $book7 = BookEntityTest::instanciateValidBook();
        $book7->setFromMember($member2);
        $book7 = Settings::getBookRepository()->insert($book7);

        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/You cant edit this book/');

        Settings::getBookController()->bookUpdate(
            (string) $book7->getId(),
            BookStatusEnum::AVAILABLE->value,
            '',
            '',
            'Description7',
            ['error' => 4],
        );
    }

    #[TestDox('BookDelete() logged redirect to /my-profile when successfull')]
    public function testBookDeleteLogged()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $book7 = Settings::getBookManager()->addBook(
            'Titre',
            'Auteur',
            '/upload/books/image.png',
            'Description',
            BookStatusEnum::AVAILABLE
        );

        $result = Settings::getBookController()->bookDelete((string) $book7->getId());

        $this->assertSame(
            303,
            $result->getHttpCode()
        );
        $this->assertSame(
            'OK',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/my-profile/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('BookDelete() not logged throw RuntimeException')]
    public function testBookDeleteNotLogged()
    {
        $book7 = Settings::getBookManager()->addBook(
            'Titre',
            'Auteur',
            '/upload/books/image.png',
            'Description',
            BookStatusEnum::AVAILABLE
        );

        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Not Logged/');

        Settings::getBookController()->bookDelete((string) $book7->getId());
    }

    #[TestDox('BookDelete() not owned throw RuntimeException')]
    public function testBookDeleteNotOwned()
    {
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2 = Settings::getMemberRepository()->insert($member2);
        $book7 = BookEntityTest::instanciateValidBook();
        $book7->setFromMember($member2);
        $book7 = Settings::getBookRepository()->insert($book7);

        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/You cant remove this book/');

        Settings::getBookController()->bookDelete((string) $book7->getId());
    }

    #[TestDox('bookAdd() logged redirect to /book-detail when successfull')]
    public function testBookAddLogged()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $fakeImage = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => '',
        ];

        $result = Settings::getBookController()->bookAdd(
            BookStatusEnum::AVAILABLE->value,
            'Titre7',
            'Auteru7',
            'Description7',
            $fakeImage,
        );

        $this->assertSame(
            303,
            $result->getHttpCode()
        );
        $this->assertSame(
            'OK',
            $result->getHttpContent()
        );
        $this->assertMatchesRegularExpression(
            '/book-detail\?bookId=/',
            $result->getHttpHeader()['Location:']
        );
    }

    #[TestDox('BookAdd() with no Title throw RuntimeException')]
    public function testBookAddNoTitle()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $fakeImage = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => '',
        ];

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/title must only contain 150 readable characters/');

        Settings::getBookController()->bookAdd(
            BookStatusEnum::AVAILABLE->value,
            '',
            'author',
            'description',
            $fakeImage
        );
    }

    #[TestDox('BookAdd() with no Author throw RuntimeException')]
    public function testBookAddNoAuthor()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $fakeImage = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => '',
        ];

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/author must only contain 150 readable characters/');

        Settings::getBookController()->bookAdd(
            BookStatusEnum::AVAILABLE->value,
            'Title',
            '',
            'description',
            $fakeImage
        );
    }

    #[TestDox('BookAdd() with no Description throw RuntimeException')]
    public function testBookAddNoDescription()
    {
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );

        $fakeImage = [
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => '',
        ];

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/description must only contain 2000 readable characters/');

        Settings::getBookController()->bookAdd(
            BookStatusEnum::AVAILABLE->value,
            'Title',
            'author',
            '',
            $fakeImage
        );
    }

    #[TestDox('BookAdd() not logged throw RuntimeException')]
    public function testBookAddNotLogged()
    {

        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Not Logged/');

        Settings::getBookController()->bookAdd(
            BookStatusEnum::AVAILABLE->value,
            'Title',
            'author',
            '',
            ['error' => 4]
        );
    }
}
