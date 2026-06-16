<?php

declare(strict_types=1);

namespace Tests\Repository;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use Green\TomTroc\Enum\MemberStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class BookRepositoryTest extends TestCase
{
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

    #[TestDox('Save a Book with valid data and delete it')]
    public function testCanSaveAndDeleteABook()
    {
        // GIVEN that table books is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // and we have this book
        $member = new MemberEntity(
            'Jean',
            'jean@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            date_create('1 days ago 12:00'),
            date_create('1 days ago 12:00'),
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);

        $book = new BookEntity(
            'Titre duLivre',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        //in the db
        Settings::getBookRepository()->insert($book);
        // $book = Settings::getBookRepository()->findByTitle($book->getTitle());

        // // EXPECT it is stored in db and there is now one row in table books
        $this->assertSame(1, count(Settings::getBookRepository()->findAll()));

        // // WHEN now, we delete it
        Settings::getBookRepository()->delete($book);

        // // EXPECT there is 0 row in book table
        $this->assertSame(0, count(Settings::getBookRepository()->findAll()));

        Settings::getMemberRepository()->delete($member);
    }

    #[TestDox('FindById and ensure getters gives the correct data')]
    public function testFindById()
    {
        // GIVEN that table books is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // and we have this member
        $member = new MemberEntity(
            'Jean',
            'jean@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            date_create('1 days ago 12:00'),
            date_create('1 days ago 12:00'),
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        // and this book
        $book = new BookEntity(
            'Titre duLivre1',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        // stored in db
        Settings::getBookRepository()->insert($book);

        // WHEN we search for it with findByTitle
        $book2 = Settings::getBookRepository()->findById($book->getId());

        // EXPECT all data retrieve are good
        $this->assertSame($book->getTitle(), $book2->getTitle());
        $this->assertSame($book->getAuthor(), $book2->getAuthor());
        $this->assertSame($book->getImagePath(), $book2->getImagePath());
        $this->assertSame($book->getDescription(), $book2->getDescription());
        $this->assertSame($book->getAvailability(), $book2->getAvailability());
        $this->assertSame($book->getFromMember()->getUsername(), $book2->getFromMember()->getUsername());
    }

    #[TestDox('FindByTitle and ensure getters gives the correct data')]
    public function testFindByTitle()
    {
        // GIVEN that table books is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // and we have this member
        $member = new MemberEntity(
            'Jean',
            'jean@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            date_create('1 days ago 12:00'),
            date_create('1 days ago 12:00'),
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        // and this book
        $book = new BookEntity(
            'Titre duLivre1',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        // stored in db
        Settings::getBookRepository()->insert($book);

        // WHEN we search for it with findByTitle
        $book2 = Settings::getBookRepository()->findByTitle($book->getTitle());

        // EXPECT all data retrieve are good
        $this->assertSame($book->getTitle(), $book2->getTitle());
        $this->assertSame($book->getAuthor(), $book2->getAuthor());
        $this->assertSame($book->getImagePath(), $book2->getImagePath());
        $this->assertSame($book->getDescription(), $book2->getDescription());
        $this->assertSame($book->getAvailability(), $book2->getAvailability());
        $this->assertSame($book->getFromMember()->getUsername(), $book2->getFromMember()->getUsername());
    }

    #[TestDox('FindAllWhere can find books by column and return the exact count of books')]
    public function testFindAllWhere()
    {
        // GIVEN that table books is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // and we have this member
        $member = new MemberEntity(
            'Jean',
            'jean@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            date_create('1 days ago 12:00'),
            date_create('1 days ago 12:00'),
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        // and these 4 books
        $book1 = new BookEntity(
            'Titre duLivre1',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        $book2 = new BookEntity(
            'Titre duLivre2',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        $book3 = new BookEntity(
            'Titre duLivre3',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        $book4 = new BookEntity(
            'Titre duLivre4',
            'MatthieudelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );

        // stored in db
        Settings::getBookRepository()->insert($book1);
        Settings::getBookRepository()->insert($book2);
        Settings::getBookRepository()->insert($book3);
        Settings::getBookRepository()->insert($book4);

        // GIVEN books are on db
        $this->assertSame(4, count(Settings::getBookRepository()->findAll()));

        // WHEN we use findAllWhere() with : author LIKE '%Jean%'
        // EXPECT we retieve 3 books
        $this->assertSame(3, count(Settings::getBookRepository()->findAllWhere('author', '=', 'JeandelaFontaine')));
    }

    #[TestDox('FindAllWhere cannot give an inexisting column')]
    public function testFindAllWhereInexistingColumn()
    {
        // GIVEN that table books is empty
        // and we have this member
        $member = new MemberEntity(
            'Jean',
            'jean@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            date_create('1 days ago 12:00'),
            date_create('1 days ago 12:00'),
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        // and these 4 books
        $book = new BookEntity(
            'Titre duLivre1',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        $book->setTitle('jean');
        $book->setAuthor('jeanjean');
        Settings::getBookRepository()->insert($book);

        // we have this book in db
        $this->assertSame(1, count(Settings::getBookRepository()->findAll()));

        // WHEN we use findAllWhere() with column that do not exist : auth LIKE '%Jean%'
        // EXPECT we retieve 0 book  // this comportement need to change
        $this->assertSame(0, count(Settings::getBookRepository()->findAllWhere('auth', 'LIKE', '%Jean%')));

        // WHEN we use findAllWhere() with operator
        // not in ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'] : author + '%Jean%'
        // EXPECT we retieve 0 book  // this comportement need to change
        $this->assertSame(0, count(Settings::getBookRepository()->findAllWhere('author', '+', '%Jean%')));
    }

    #[TestDox('Update a book and ensure getters send the same data')]
    public function testUpdate()
    {
        // GIVEN that table books is empty
        $this->assertTrue(count(Settings::getBookRepository()->findAll()) === 0);
        // and this user exist in db
        $member = new MemberEntity(
            'Jean',
            'jean@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            date_create('1 days ago 12:00'),
            date_create('1 days ago 12:00'),
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        // and this book
        $book = new BookEntity(
            'Titre duLivre1',
            'JeandelaFontaine',
            '/upload/books/titreDuLivre.png',
            'cest une histoire affabulante',
            BookStatusEnum::AVAILABLE,
            $member
        );
        $book->setTitle('jean');
        $book->setAuthor('jeanjean');
        Settings::getBookRepository()->insert($book);

        // WHEN we update it on db
        $book2 = Settings::getBookRepository()->findByTitle($book->getTitle());
        $book2->setTitle('Autobiographie de John Doe');
        $book2->setAuthor('John Doe');
        $book2->setImagePath('/upload/books/johndoe.png');
        $book2->setDescription('cest incroyable');
        $book2->setAvailability(BookStatusEnum::NOTAVAILABLE);

        Settings::getBookRepository()->update($book2->getId(), $book2);

        // EXPECT getters give the data updated
        $this->assertSame('Autobiographie de John Doe', $book2->getTitle());
        $this->assertSame('John Doe', $book2->getAuthor());
        $this->assertSame('/upload/books/johndoe.png', $book2->getImagePath());
        $this->assertSame('cest incroyable', $book2->getDescription());
        $this->assertSame(BookStatusEnum::NOTAVAILABLE, $book2->getAvailability());
    }
}
