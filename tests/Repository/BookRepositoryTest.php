<?php

declare(strict_types=1);

namespace Tests\Repository;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\BookEntityTest;
use Tests\Entity\MemberEntityTest;

class BookRepositoryTest extends TestCase
{
    // PHPunit fixtures
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
    }

    public function tearDown(): void
    {
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    public static function tearDownAfterClass(): void
    {
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    #[TestDox('insert() with valid Given Data return BookEntity with the last insert Id')]
    public function testInsertBook(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // And have a bookEntity with his member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);
        $book = new BookEntity(
            'Titre duLivre',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );

        // WHEN
        // insert() it in db
        $book = Settings::getBookRepository()->insert($book);

        // EXPECT
        // return BookEntity with lastId inserted
        $this->assertSame('Green\TomTroc\Entity\BookEntity', $book::class);
        // And there is now one row in books table
        $this->assertSame(1, count(Settings::getBookRepository()->findAll()));
        $this->assertSame($member->getId(), $book->getFromMember()->getId());
        $this->assertSame($member->getUserName(), $book->getFromMember()->getUserName());
        $this->assertSame('Titre duLivre', $book->getTitle());
        $this->assertSame('JeandelaFontaine', $book->getAuthor());
        $this->assertSame('/upload/books/titreDuLivre.png', $book->getImagePath());
        $this->assertSame('cest une histoire affabulante', $book->getDescription());
        $this->assertSame(BookStatusEnum::AVAILABLE->value, $book->getAvailability()->value);
        $this->assertSame($member->getId(), $book->getFromMember()->getId());
    }

    #[TestDox('insert() with a BookEntity that already have a valid bookId throw RuntimeException')]
    public function testInsertBookWithValidId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // And have a bookEntity with his member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);
        $book = new BookEntity(
            'Titre duLivre',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        $book = Settings::getBookRepository()->insert($book);

        // EXPECT
        // throw Runtime Exception
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('This book already inserted');

        // WHEN
        // insert() it in db
        $result = Settings::getBookRepository()->insert($book);
    }

    #[TestDox('insert() with a non-existent FromMember memberId throw exception')]
    public function testInsertWithoutMemberIdException(): void
    {
        // GIVEN
        // have a book with his member
        $member = MemberEntityTest::instanciateValidMember();
        $book = new BookEntity(
            'Titre duLivre',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );

        $this->assertNull($member->getId());

        // EXPECT
        // throw Runtime Exception
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Member Id is null/');

        // WHEN
        // insert() it in db
        $result = Settings::getBookRepository()->insert($book);
    }

    #[TestDox('update() with valid Given Data return a valid BookEntity updated')]
    public function testUpdate(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member = Settings::getMemberRepository()->insert(
            MemberEntityTest::instanciateValidMember()
        );
        // and this book
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        $book = Settings::getBookRepository()->insert($book);

        // WHEN
        // update() it
        $book2 = Settings::getBookRepository()->update(
            $book->getId(),
            new BookEntity(
                'Titre mis a jour',
                'auteur mis a jour',
                '/upload/books/johndoemaj.png',
                'description mis a jour',
                BookStatusEnum::NOTAVAILABLE,
                $member
            )
        );

        // EXPECT
        // getters give the data updated
        $this->assertSame($book->getId(), $book2->getId());
        $this->assertSame($book->getFromMember()->getId(), $book2->getFromMember()->getId());
        $this->assertSame($book->getFromMember()->getUserName(), $book2->getFromMember()->getUserName());
        $this->assertSame('Titre mis a jour', $book2->getTitle());
        $this->assertSame('auteur mis a jour', $book2->getAuthor());
        $this->assertSame('/upload/books/johndoemaj.png', $book2->getImagePath());
        $this->assertSame('description mis a jour', $book2->getDescription());
        $this->assertSame(BookStatusEnum::NOTAVAILABLE, $book2->getAvailability());
    }

    #[TestDox('update() with inexistent bookId given throw RuntimeException')]
    public function testUpdateInexistentBookId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and this book
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('bookId doesnt exist');

        // WHEN
        // update() it
        $book2 = Settings::getBookRepository()->update(76, $book);
    }

    #[TestDox('update() with inexistent FromMember memberId given throw RuntimeException')]
    public function testUpdateInexistentFromMemberId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('Username2Username');
        Settings::getMemberRepository()->insert($member2);

        $member = MemberEntityTest::instanciateValidMember();
        $this->assertNull($member->getId());

        // and this book
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member2);
        $book = Settings::getBookRepository()->insert($book);

        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setFromMember($member);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('Null memberId in the given FromMember');

        // WHEN
        // update() it
        $book2 = Settings::getBookRepository()->update($book->getId(), $book2);
    }

    #[TestDox('update() with bookId mismatch with BookEntity\'s bookId given throw RuntimeException')]
    public function testUpdateMismatchBookId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);
        // and this book
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        $book = Settings::getBookRepository()->insert($book);

        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setFromMember($member);
        $book2->setTitle('Book2Book2');
        $book2 = Settings::getBookRepository()->insert($book2);

        $this->assertNotSame($book->getId(), $book2->getId());

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('bookId mismatch with bookId whithin the given BookEntity');

        // WHEN
        // update() it
        Settings::getBookRepository()->update($book->getId(), $book2);
    }

    #[TestDox('delete() with valid bookId return True')]
    public function testDeleteBook(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // And have a book with his member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);
        $book = new BookEntity(
            'Titre duLivre',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        $book = Settings::getBookRepository()->insert($book);
        $this->assertSame(1, count(Settings::getBookRepository()->findAll()));

        // WHEN
        // now, delete() it
        $result = Settings::getBookRepository()->delete($book);

        // EXPECT
        // return true
        $this->assertTrue($result);
        // And there is now 0 row in books table
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
    }

    #[TestDox('delete() with null bookId throw RuntimeException')]
    public function testDeleteBookWithInexistentId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // And have a book with his member in db
        $member = MemberEntityTest::instanciateValidMember();
        $book = new BookEntity(
            'Titre duLivre',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('bookId is null');

        // WHEN
        // now, delete() it
        Settings::getBookRepository()->delete($book);
    }

    #[TestDox('findOneById() with an existing bookId return a valid BookEntity')]
    public function testFindOneById(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and this book in db
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        Settings::getBookRepository()->insert($book);

        // WHEN
        // findOneById()
        $book2 = Settings::getBookRepository()->findOneById($book->getId());

        // EXPECT
        // findOneById() show $book informations
        $this->assertSame($book->getTitle(), $book2->getTitle());
        $this->assertSame($book->getAuthor(), $book2->getAuthor());
        $this->assertSame($book->getImagePath(), $book2->getImagePath());
        $this->assertSame($book->getDescription(), $book2->getDescription());
        $this->assertSame($book->getAvailability(), $book2->getAvailability());
        $this->assertSame($book->getFromMember()->getId(), $book2->getFromMember()->getId());
    }

    #[TestDox('findOneById() with an inexisting bookId return null')]
    public function testFindOneByIdWithInexistingBookId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and this book in db
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        Settings::getBookRepository()->insert($book);

        // WHEN
        // findOneById() with inexistent bookId
        $result = Settings::getBookRepository()->findOneById(72);

        // EXPECT
        // findOneById() return null
        $this->assertNull($result);
    }

    #[TestDox('FindAll() with N books in db return an array of N number of Books')]
    public function testFindAll(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // And we have this member
        $member = MemberEntityTest::instanciateValidMember();
        // In db
        Settings::getMemberRepository()->insert($member);
        // And 2 books
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setTitle('Titre1');
        $book1->setAuthor('Jean de la Fontaine');
        $book1->setFromMember($member);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setTitle('Titre2');
        $book2->setAuthor('Jean Jacques Rousseau');
        $book2->setFromMember($member);
        // In db
        Settings::getBookRepository()->insert($book1);
        Settings::getBookRepository()->insert($book2);

        // WHEN
        // Use findAll()
        $result = Settings::getBookRepository()->findAll();
        // EXPECT
        // retieve 2 books
        $this->assertTrue(is_array($result));
        $this->assertSame(2, count(Settings::getBookRepository()->findAll()));
    }

    #[TestDox('FindAll() with empty db return an array of O Book')]
    public function testFindAllWithZeroBooksInDb(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);

        // WHEN
        // Use findAll()
        $result = Settings::getBookRepository()->findAll();
        // EXPECT
        // retieve 0 books
        $this->assertTrue(is_array($result));
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
    }

    #[TestDox('findAllLast() return an array of N number of last Books')]
    public function testFindAllLast(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // And have this member
        $member = MemberEntityTest::instanciateValidMember();
        // In db
        Settings::getMemberRepository()->insert($member);
        // And 4 books
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setTitle('Titre1');
        $book1->setAuthor('Jean de la Fontaine');
        $book1->setFromMember($member);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setTitle('Titre2');
        $book2->setAuthor('Jean Jacques Rousseau');
        $book2->setFromMember($member);
        $book3 = BookEntityTest::instanciateValidBook();
        $book3->setTitle('Titre3');
        $book3->setAuthor('Jean Guy');
        $book3->setFromMember($member);
        $book4 = BookEntityTest::instanciateValidBook();
        $book4->setTitle('Titre4');
        $book4->setAuthor('Marcel Pagnol');
        $book4->setFromMember($member);
        // In db
        Settings::getBookRepository()->insert($book1);
        Settings::getBookRepository()->insert($book2);
        Settings::getBookRepository()->insert($book3);
        Settings::getBookRepository()->insert($book4);

        // WHEN
        // Use findAll()
        // EXPECT
        // retieve 4 books
        $this->assertSame(4, count(Settings::getBookRepository()->findAll()));

        // WHEN
        // Use findAllLast(2)
        // EXPECT
        // retieve 2 books
        $this->assertSame(2, count(Settings::getBookRepository()->findAllLast(2)));
    }

    #[TestDox('findAllLast(2) with empty db return an array of 0 number of last Books')]
    public function testFindAllLastWithZeroBooksInDb(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));

        // WHEN
        // Use findAllLast(2)
        // EXPECT
        // retieve 0 books
        $this->assertSame(0, count(Settings::getBookRepository()->findAllLast(2)));
    }

    #[TestDox('FindAllByTitle() with title that exist in 1 book in the db return an array of 1 BookEntity')]
    public function testFindAllByTitle(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and this book in db
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        Settings::getBookRepository()->insert($book);

        // WHEN
        // findAllByTitle()
        $result = Settings::getBookRepository()->findAllByTitle($book->getTitle());

        // EXPECT
        // findAllByTitle() return an array of 1 book
        $this->assertSame(1, count($result));
        $this->assertSame($book->getTitle(), $result[0]->getTitle());
        $this->assertSame($book->getAuthor(), $result[0]->getAuthor());
        $this->assertSame($book->getImagePath(), $result[0]->getImagePath());
        $this->assertSame($book->getDescription(), $result[0]->getDescription());
        $this->assertSame($book->getAvailability(), $result[0]->getAvailability());
        $this->assertSame($book->getFromMember()->getId(), $result[0]->getFromMember()->getId());
    }

    #[TestDox('FindAllByTitle() with a title that doesnt exist in the db return an array of 0')]
    public function testFindAllByTitleInexisting(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));

        // WHEN
        // findAllByTitle() with inexisting title
        $result = Settings::getBookRepository()->findAllByTitle('untitre');

        // EXPECT
        // findAllByTitle() return an array of 0 element
        $this->assertSame(0, count($result));
    }

    #[TestDox('FindAllByMember() with a member that own N books return an array of N Books')]
    public function testFindAllByMember(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);
        // and these 2 books
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setTitle('Titre2');
        $book2->setFromMember($member);
        // stored in db
        Settings::getBookRepository()->insert($book);
        Settings::getBookRepository()->insert($book2);

        // WHEN
        // findAllByMember()
        $arrayBook = Settings::getBookRepository()->findAllByMember($member);

        // EXPECT
        // retrieve 2 books
        $this->assertSame(2, count($arrayBook));
    }

    #[TestDox('FindAllByMember() with a member that own 0 Book return an array of 0 Books')]
    public function testFindAllByMemberWhenZeroInDb(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);

        // WHEN
        // findAllByMember()
        $arrayBook = Settings::getBookRepository()->findAllByMember($member);

        // EXPECT
        // retrieve 0 books
        $this->assertSame(0, count($arrayBook));
    }

    #[TestDox('FindAllByMember() with inexisting memberId return an array of 0 Books')]
    public function testFindAllByMemberWhenInexistingMemberId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);

        // WHEN
        // findAllByMember()
        $arrayBook = Settings::getBookRepository()->findAllByMember(75);

        // EXPECT
        // retrieve 0 books
        $this->assertSame(0, count($arrayBook));
    }

    #[TestDox('FindAllByAvailability() with a given status return an array of N Books that have this status')]
    public function testFindByAvailability(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and these 2 books
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        $book->setAvailability(BookStatusEnum::AVAILABLE);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setTitle('Titre2');
        $book2->setFromMember($member);
        $book2->setAvailability(BookStatusEnum::NOTAVAILABLE);
        // stored in db
        Settings::getBookRepository()->insert($book);
        Settings::getBookRepository()->insert($book2);

        // WHEN
        // findOneByMember()
        $arrayBook = Settings::getBookRepository()->findAllByAvailability(BookStatusEnum::AVAILABLE);

        // EXPECT
        // retrieve 1 books
        $this->assertSame(1, count($arrayBook));
    }
}
