<?php

declare(strict_types=1);

namespace Tests\Manager;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Tests\Entity\BookEntityTest;
use Tests\Entity\MemberEntityTest;

class BookManagerTest extends TestCase
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

    #[TestDox('Logged in Members can add book to their personnal library')]
    #[TestWith(['John Doe', 'johndoe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testLoggedInMembersCanAddBookToTheirLibrary(
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ) {
        // GIVEN
        // Have 1 members registered :
        Settings::getMemberManager()->register($username, $email, $password, $avatarPath);
        $member = Settings::getMemberRepository()->findByEmail('johndoe@mail.com');

        // $member is logged in
        Settings::getMemberManager()->login($email, $password);

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->addBook(
            $member,
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
        // EXPECT addBook return true
        $this->assertTrue($result);
    }

    #[TestDox('Not Logged in Members cannot add book to their personnal library')]
    #[TestWith(['John Doe', 'johndoe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testNotLoggedInMembersCanNotAddBookToTheirLibrary(
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ) {
        // GIVEN
        // Have 1 members registered :
        // Settings::getMemberManager()->register($username, $email, $password, $avatarPath);
        // $member = Settings::getMemberRepository()->findByEmail('johndoe@mail.com');
        $member = MemberEntityTest::instanciateValidMember();

        // EXPECT
        // Have a RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Member Id is null/');

        // WHEN member add a book to his library
        $result = Settings::getBookManager()->addBook(
            $member,
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
    }

    #[TestDox('Logged in Members can list books from their personnal library')]
    #[TestWith(['John Doe', 'johndoe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testLoggedInMembersCanListBooksFromTheirLibrary(
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ) {
        // GIVEN
        // Have 1 member registered :
        Settings::getMemberManager()->register($username, $email, $password, $avatarPath);
        $member = Settings::getMemberRepository()->findByEmail('johndoe@mail.com');

        // $member is logged in
        Settings::getMemberManager()->login($email, $password);

        // $member add a book to his library
        $result = Settings::getBookManager()->addBook(
            $member,
            'Un Livre',
            'Un Auteur',
            '/upload/books/book.png',
            'Une Description',
            BookStatusEnum::AVAILABLE
        );
        $result = Settings::getBookManager()->addBook(
            $member,
            'Un Livre2',
            'Un Auteur2',
            '/upload/books/book2.png',
            'Une Description2',
            BookStatusEnum::NOTAVAILABLE
        );

        // WHEN member list his books
        $result = Settings::getBookManager()->getMyLibrary($member);

        // EXPECT getMyLibrary return array with books
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 2);
    }

    #[TestDox('Not Logged in Members can list available books')]
    #[TestWith(['John Doe', 'johndoe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testNotLoggedInMembersCanListBooks(
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ) {
        // GIVEN
        // Have 1 member registered :
        Settings::getMemberManager()->register($username, $email, $password, $avatarPath);
        $member = Settings::getMemberRepository()->findByEmail('johndoe@mail.com');

        // $member add a book to his library
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setAvailability(BookStatusEnum::AVAILABLE);
        $book1->setFromMember($member);
        $book2 = BookEntityTest::instanciateValidBook();
        $book2->setFromMember($member);
        $book2->setAvailability(BookStatusEnum::AVAILABLE);
        Settings::getBookRepository()->insert($book1);
        Settings::getBookRepository()->insert($book2);

        // WHEN user listAvailableBook()
        $result = Settings::getBookManager()->listAvailableBook();

        // EXPECT getMyLibrary return array with books
        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));
    }

    #[TestDox('Not Logged in Members can get books details')]
    #[TestWith(['John Doe', 'johndoe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testNotLoggedInMembersCanSeeBookDetail(
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ) {
        // GIVEN
        // Have 1 member registered :
        Settings::getMemberManager()->register($username, $email, $password, $avatarPath);
        $member = Settings::getMemberRepository()->findByEmail('johndoe@mail.com');

        // $member add a book to his library
        $book1 = BookEntityTest::instanciateValidBook();
        $book1->setAvailability(BookStatusEnum::AVAILABLE);
        $book1->setFromMember($member);
        Settings::getBookRepository()->insert($book1);

        // WHEN user listAvailableBook()
        $result = Settings::getBookManager()->getBookDetail($book1->getId());

        // EXPECT getMyLibrary return array with books
        $this->assertSame($book1->getTitle(), $result->getTitle());
        $this->assertSame($book1->getId(), $result->getId());
    }
}
