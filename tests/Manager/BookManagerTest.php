<?php

declare(strict_types=1);

namespace Tests\Manager;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\BookEntityTest;

class BookManagerTest extends TestCase
{
    private MemberEntity $member1;

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

        $this->member1 = Settings::getAuthentificationService()->register(
            'John Doe',
            'johndoe@mail.com',
            'Johndoe2026*',
            '/upload/avatars/johndoe.png'
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

    #[TestDox('addBook() when Logged will add book to their personnal library')]
    public function testAddBookLogged()
    {
        // GIVEN
        // have logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->addBook(
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
        // EXPECT addBook return true
        $this->assertSame('Green\TomTroc\Entity\BookEntity', $result::class);
        $this->assertSame($this->member1->getUserName(), $result->getFromMember()->getUserName());
    }

    #[TestDox('addBook() when not Logged throw RuntimeException')]
    public function testAddBookNotLogged()
    {
        // GIVEN
        // have logged in
        Settings::getAuthentificationService()->logout();

        // EXPECT addBook throw Runtime Exception
        $this->expectException('RunTimeException');
        $this->expectExceptionMessageIsOrContains('You are not logged in');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->addBook(
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
    }

    #[TestDox('addBook() when Logged and empty title throw RunTimeException')]
    public function testAddBookLoggedEmptyTitle()
    {
        // GIVEN
        // have logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // EXPECT addBook throw Runtime Exception
        $this->expectException('RunTimeException');
        $this->expectExceptionMessageIsOrContains('Invalid title : title must only contain 150 readable characters');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->addBook(
            '',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
    }

    #[TestDox('addBook() when Logged and empty author throw RunTimeException')]
    public function testAddBookLoggedEmptyAuthor()
    {
        // GIVEN
        // have logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // EXPECT addBook throw Runtime Exception
        $this->expectException('RunTimeException');
        $this->expectExceptionMessageIsOrContains('Invalid author : author must only contain 150 readable characters');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->addBook(
            'Un Titre',
            '',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
    }

    #[TestDox('addBook() when Logged and empty imagePath throw RunTimeException')]
    public function testAddBookLoggedEmptyImagePath()
    {
        // GIVEN
        // have logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // EXPECT addBook throw Runtime Exception
        $this->expectException('RunTimeException');
        $this->expectExceptionMessageIsOrContains('Invalid imagePath : imagePath must be stored in /upload/books/,');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->addBook(
            'Un Titre',
            'Un Auteur',
            '',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
    }

    #[TestDox('addBook() when Logged and empty description throw RunTimeException')]
    public function testAddBookLoggedEmptyDescription()
    {
        // GIVEN
        // have logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // EXPECT addBook throw Runtime Exception
        $this->expectException('RunTimeException');
        $this->expectExceptionMessageIsOrContains(
            'Invalid description : description must only contain 2000 readable characters'
        );

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->addBook(
            'Un Titre',
            'Un Auteur',
            '/upload/books/book.png',
            '',
            BookStatusEnum::AVAILABLE
        );
    }

    #[TestDox('updateBook() when Logged will add book to their personnal library')]
    public function testUpdateBookLogged()
    {
        // GIVEN
        // have logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        $book1 = Settings::getBookManager()->addBook(
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
        $book1->setTitle('Nouveau Titre');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->updateBook(
            $book1
        );
        // EXPECT updateBook return true
        $this->assertSame('Green\TomTroc\Entity\BookEntity', $result::class);
        $this->assertSame($book1->getFromMember()->getUserName(), $result->getFromMember()->getUserName());
        $this->assertSame($book1->getId(), $result->getId());
        $this->assertSame($book1->getTitle(), $result->getTitle());
    }

    #[TestDox('updateBook() when not Logged throw RuntimeException')]
    public function testUpdateBookNotLogged()
    {
        // GIVEN
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');
        $book1 = Settings::getBookManager()->addBook(
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
        $book1->setTitle('Nouveau Titre');
        // have logged out
        Settings::getAuthentificationService()->logout();

        // EXPECT updateBook throw Runtime Exception
        $this->expectException('RunTimeException');
        $this->expectExceptionMessageIsOrContains('You are not logged in');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->updateBook(
            $book1
        );
    }

    #[TestDox('deleteBook() when Logged will add book to their personnal library')]
    public function testDeleteBookLogged()
    {
        // GIVEN
        // have logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        $book1 = Settings::getBookManager()->addBook(
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->deleteBook(
            $book1
        );
        // EXPECT deleteBook return true
        $this->assertSame(true, $result);
    }

    #[TestDox('deleteBook() when not Logged throw RuntimeException')]
    public function testDeleteBookNotLogged()
    {
        // GIVEN
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');
        $book1 = Settings::getBookManager()->addBook(
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
        $book1->setTitle('Nouveau Titre');
        // have logged out
        Settings::getAuthentificationService()->logout();

        // EXPECT deleteBook throw Runtime Exception
        $this->expectException('RunTimeException');
        $this->expectExceptionMessageIsOrContains('You are not logged in');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->deleteBook(
            $book1
        );
    }

    #[TestDox('getMyLibrary() when logged return an array of own books')]
    public function testGetMyLibraryLogged()
    {
        // GIVEN
        // Have 1 logged member
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // $member add a book to his library
        Settings::getBookManager()->addBook(
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
        Settings::getBookManager()->addBook(
            'Un Livre2',
            'Un Auteur2',
            '/upload/books/book2.png',
            'Une Description2',
            BookStatusEnum::NOTAVAILABLE
        );

        // WHEN member list his books
        $result = Settings::getBookManager()->getMyLibrary();

        // EXPECT getMyLibrary return array with books
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 2);
    }

    #[TestDox('getMyLibrary() when not logged throw RuntimeException')]
    public function testGetMyLibraryNotLogged()
    {
        // GIVEN
        // Have 1 logged member
        Settings::getAuthentificationService()->logout();

        // EXPECT addBook throw Runtime Exception
        $this->expectException('RunTimeException');
        $this->expectExceptionMessageIsOrContains('You are not logged in');

        // WHEN member list his books
        $result = Settings::getBookManager()->getMyLibrary();
    }

    #[TestDox('listBooks() when Not Logged return an array of all books')]
    public function testNotLoggedInMembersCanListBooks()
    {
        // GIVEN
        // Have 1 member registered :
        $member = Settings::getAuthentificationService()->logout();

        // $member add a book to his library
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setAvailability(BookStatusEnum::AVAILABLE);
        $book1->setFromMember($this->member1);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setFromMember($this->member1);
        $book2->setAvailability(BookStatusEnum::AVAILABLE);
        $book1 = Settings::getBookRepository()->insert($book1);
        $book2 = Settings::getBookRepository()->insert($book2);

        // WHEN user listBooks()
        $result = Settings::getBookManager()->listBooks();

        // EXPECT getMyLibrary return array with books
        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));
    }

    #[TestDox('listBooks() when Logged return an array of all books')]
    public function testLoggedInMembersCanListBooks()
    {
        // GIVEN
        // Have 1 member registered :
        $member = Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // $member add a book to his library
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setAvailability(BookStatusEnum::AVAILABLE);
        $book1->setFromMember($this->member1);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setFromMember($this->member1);
        $book2->setAvailability(BookStatusEnum::AVAILABLE);
        $book1 = Settings::getBookRepository()->insert($book1);
        $book2 = Settings::getBookRepository()->insert($book2);

        // WHEN user listBooks()
        $result = Settings::getBookManager()->listBooks();

        // EXPECT getMyLibrary return array with books
        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));
    }

    #[TestDox('listLastBook() when not Logged return an array of N Last books')]
    public function testNotLoggedInMembersCanListLastBooks()
    {
        // GIVEN
        $member = Settings::getAuthentificationService()->logout();

        // $member add a book to his library
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setAvailability(BookStatusEnum::AVAILABLE);
        $book1->setFromMember($this->member1);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setFromMember($this->member1);
        $book2->setAvailability(BookStatusEnum::AVAILABLE);
        $book3 = BookEntityTest::instanciateValidBook();
        $book3->setAvailability(BookStatusEnum::AVAILABLE);
        $book3->setFromMember($this->member1);
        $book4 = BookEntityTest::instanciateValidBook();
        $book4->setFromMember($this->member1);
        $book4->setAvailability(BookStatusEnum::AVAILABLE);
        $book1 = Settings::getBookRepository()->insert($book1);
        $book2 = Settings::getBookRepository()->insert($book2);
        $book3 = Settings::getBookRepository()->insert($book3);
        $book4 = Settings::getBookRepository()->insert($book4);

        // WHEN user listLastBook(2)
        $result = Settings::getBookManager()->listLastBook(2);

        // EXPECT getMyLibrary return array with books
        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));
    }

    #[TestDox('listLastBook() when Logged return an array of N Last books')]
    public function testLoggedInMembersCanListLastBooks()
    {
        // GIVEN
        $member = Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');
        // $member add a book to his library
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setAvailability(BookStatusEnum::AVAILABLE);
        $book1->setFromMember($this->member1);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setFromMember($this->member1);
        $book2->setAvailability(BookStatusEnum::AVAILABLE);
        $book3 = BookEntityTest::instanciateValidBook();
        $book3->setAvailability(BookStatusEnum::AVAILABLE);
        $book3->setFromMember($this->member1);
        $book4 = BookEntityTest::instanciateValidBook();
        $book4->setFromMember($this->member1);
        $book4->setAvailability(BookStatusEnum::AVAILABLE);
        $book1 = Settings::getBookRepository()->insert($book1);
        $book2 = Settings::getBookRepository()->insert($book2);
        $book3 = Settings::getBookRepository()->insert($book3);
        $book4 = Settings::getBookRepository()->insert($book4);

        // WHEN user listLastBook(2)
        $result = Settings::getBookManager()->listLastBook(2);

        // EXPECT getMyLibrary return array with books
        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));
    }

    #[TestDox('getBookDetail() when Not Logged a valid BookEntity')]
    public function testNotLoggedInMembersCanSeeBookDetail()
    {
        // GIVEN
        // Have 1 member registered :
        $member = Settings::getAuthentificationService()->logout();

        // $member add a book to his library
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setAvailability(BookStatusEnum::AVAILABLE);
        $book1->setFromMember($this->member1);
        Settings::getBookRepository()->insert($book1);

        // WHEN user getBookDetail()
        $result = Settings::getBookManager()->getBookDetail($book1->getId());

        // EXPECT getBookDetail return array with books
        $this->assertSame($book1->getTitle(), $result->getTitle());
        $this->assertSame($book1->getId(), $result->getId());
    }

    #[TestDox('getBookDetail() when Logged a valid BookEntity')]
    public function testLoggedInMembersCanSeeBookDetail()
    {
        // GIVEN
        // Have 1 member registered :
        $member = Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // $member add a book to his library
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setAvailability(BookStatusEnum::AVAILABLE);
        $book1->setFromMember($this->member1);
        Settings::getBookRepository()->insert($book1);

        // WHEN user getBookDetail()
        $result = Settings::getBookManager()->getBookDetail($book1->getId());

        // EXPECT getBookDetail return array with books
        $this->assertSame($book1->getTitle(), $result->getTitle());
        $this->assertSame($book1->getId(), $result->getId());
    }
}
