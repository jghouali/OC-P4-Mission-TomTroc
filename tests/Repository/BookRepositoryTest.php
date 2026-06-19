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

    #[TestDox('insert() and delete()')]
    public function testInsertAndDeleteBook(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // And have a book with his member
        $member = MemberEntityTest::instanciateValidMember();
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
        Settings::getMemberRepository()->insert($member);
        $result = Settings::getBookRepository()->insert($book);

        // EXPECT
        // return true
        $this->assertTrue($result);
        // And there is now one row in books table
        $bookArray = Settings::getBookRepository()->findAll();
        $this->assertSame(1, count($bookArray));
        $this->assertSame($book->getTitle(), $bookArray[0]['title']);
        $this->assertSame($book->getAuthor(), $bookArray[0]['author']);
        $this->assertSame($book->getImagePath(), $bookArray[0]['image_path']);
        $this->assertSame($book->getDescription(), $bookArray[0]['description']);
        $this->assertSame($book->getAvailability()->value, $bookArray[0]['availability']);
        $this->assertSame($book->getFromMember()->getId(), $bookArray[0]['fk_member_id']);

        // WHEN
        // now, delete() it
        $result2 = Settings::getBookRepository()->delete($book);

        // EXPECT
        // return true
        $this->assertTrue($result2);
        // And there is now 0 row in books table
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
    }

    #[TestDox('insert() a Book with a member_id null throw exception')]
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

        // EXPECT
        // return true
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Member Id is null/');

        // WHEN
        // insert() it in db
        $result = Settings::getBookRepository()->insert($book);
    }

    #[TestDox('FindAll() books')]
    public function testFindAll(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
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
        // EXPECT
        // retieve 2 books
        $this->assertSame(2, count(Settings::getBookRepository()->findAll()));
    }

    #[TestDox('FindAllWhere(\'author\', \'LIKE\', \'%Jean%\') books')]
    public function testFindAllWhere(): void
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
        // Use findAllWhere() with : author LIKE '%Jean%'
        // EXPECT
        // retieve 3 books
        $this->assertSame(3, count(Settings::getBookRepository()->findAllWhere('author', 'LIKE', '%Jean%')));
    }

    #[TestDox('FindAllWhere() return [] with invalid informations')]
    public function testFindAllWhereInvalidData(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and this books
        $book = BookEntityTest::instanciateValidBook();
        $book->setTitle('jean');
        $book->setAuthor('jeanjean');
        $book->setFromMember($member);
        // in db
        Settings::getBookRepository()->insert($book);
        $this->assertSame(1, count(Settings::getBookRepository()->findAll()));

        // WHEN
        // findAllWhere() with column that does not exist : auth LIKE '%Jean%'
        // EXPECT
        // retieve 0 book  // this comportement may change
        $this->assertSame(0, count(Settings::getBookRepository()->findAllWhere('auth', 'LIKE', '%Jean%')));

        // WHEN
        // findAllWhere() with operator
        // not in ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'] : author + '%Jean%'
        // EXPECT
        // retieve 0 book  // this comportement may change
        $this->assertSame(0, count(Settings::getBookRepository()->findAllWhere('author', '+', '%Jean%')));
    }

    #[TestDox('FindById()')]
    public function testFindById(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and this book in db
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        Settings::getBookRepository()->insert($book);

        // WHEN
        // findById()
        $book2 = Settings::getBookRepository()->findById($book->getId());

        // EXPECT
        // findById() show $book informations
        $this->assertSame($book->getTitle(), $book2->getTitle());
        $this->assertSame($book->getAuthor(), $book2->getAuthor());
        $this->assertSame($book->getImagePath(), $book2->getImagePath());
        $this->assertSame($book->getDescription(), $book2->getDescription());
        $this->assertSame($book->getAvailability(), $book2->getAvailability());
        $this->assertSame($book->getFromMember()->getUsername(), $book2->getFromMember()->getUsername());
    }

    #[TestDox('FindByTitle()')]
    public function testFindByTitle(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and this book in db
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        Settings::getBookRepository()->insert($book);

        // WHEN
        // findByTitle()
        $book2 = Settings::getBookRepository()->findByTitle($book->getTitle());

        // EXPECT
        // findByTitle() show $book informations
        $this->assertSame($book->getTitle(), $book2->getTitle());
        $this->assertSame($book->getAuthor(), $book2->getAuthor());
        $this->assertSame($book->getImagePath(), $book2->getImagePath());
        $this->assertSame($book->getDescription(), $book2->getDescription());
        $this->assertSame($book->getAvailability(), $book2->getAvailability());
        $this->assertSame($book->getFromMember()->getUsername(), $book2->getFromMember()->getUsername());
    }

    #[TestDox('FindAllByMember()')]
    public function testFindAllByMember(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
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
        // findByMember()
        $arrayBook = Settings::getBookRepository()->findAllByMember($member);

        // EXPECT
        // retrieve 2 books
        $this->assertTrue(count($arrayBook) === 2);
    }

    #[TestDox('FindAllByAvailability()')]
    public function testFindByAvailability(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
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
        // findByMember()
        $arrayBook = Settings::getBookRepository()->findAllByAvailability(BookStatusEnum::AVAILABLE);

        // EXPECT
        // retrieve 2 books
        $this->assertTrue(count($arrayBook) === 1);
    }

    #[TestDox('update()')]
    public function testUpdate(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // and we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        // and this book
        $book = BookEntityTest::instanciateValidBook();
        $book->setFromMember($member);
        Settings::getBookRepository()->insert($book);

        // WHEN
        // update() it
        $book2 = Settings::getBookRepository()->findByTitle($book->getTitle());
        $book2->setTitle('Titre mis a jour');
        $book2->setAuthor('auteur mis a jour');
        $book2->setImagePath('/upload/books/johndoemaj.png');
        $book2->setDescription('description mis a jour');
        $book2->setAvailability(BookStatusEnum::NOTAVAILABLE);
        Settings::getBookRepository()->update($book2->getId(), $book2);

        // EXPECT
        // getters give the data updated
        $this->assertSame('Titre mis a jour', $book2->getTitle());
        $this->assertSame('auteur mis a jour', $book2->getAuthor());
        $this->assertSame('/upload/books/johndoemaj.png', $book2->getImagePath());
        $this->assertSame('description mis a jour', $book2->getDescription());
        $this->assertSame(BookStatusEnum::NOTAVAILABLE, $book2->getAvailability());
    }
}
