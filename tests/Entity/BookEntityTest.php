<?php

declare(strict_types=1);

namespace Tests\Entity;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use Green\TomTroc\Enum\MemberStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BookEntityTest extends TestCase
{
    private BookEntity $validBook;

    // PHPunit fixtures
    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
    }

    public function tearDown(): void
    {
        unset($this->validBook);
    }

    public function setUp(): void
    {
        $this->validBook = self::instanciateValidBook();
    }

    public static function instanciateValidBook(): BookEntity
    {
        return new BookEntity(
            'Titre du livre',
            'John Doe',
            '/upload/books/book.png',
            'Cest un livre',
            BookStatusEnum::AVAILABLE,
            MemberEntityTest::instanciateValidMember(),
        );
    }

    public function testBookEntityConstructor(): void
    {
        // GIVEN
        // Have this informations about a book :
        // 'Titre du livre', 'John Doe', '/upload/books/book.png',
        // 'Cest un livre', BookStatusEnum::AVAILABLE, fromMember 'John Doe'

        // WHEN
        // Create BookEntity using constructor
        $book = new BookEntity(
            'Titre du livre',
            'John Doe',
            '/upload/books/book.png',
            'Cest un livre',
            BookStatusEnum::AVAILABLE,
            MemberEntityTest::instanciateValidMember()
        );

        // EXPECT
        // $book is a child of Green\TomTroc\Entity\BookEntity
        $this->assertSame('Green\TomTroc\Entity\BookEntity', get_class($book));
        // can getTitle()
        $this->assertSame('Titre du livre', $book->getTitle());
        // can getAuthor()
        $this->assertSame('John Doe', $book->getAuthor());
        // can getImagePath()
        $this->assertSame('/upload/books/book.png', $book->getImagePath());
        // can getDescription()
        $this->assertSame('Cest un livre', $book->getDescription());
        // can getAvailability()
        $this->assertSame(BookStatusEnum::AVAILABLE, $book->getAvailability());
        // can getFromMember()
        $this->assertSame('John Doe', $book->getFromMember()->getUsername());
    }

    public function testGetters(): void
    {
        // GIVEN
        // Have a $this->validBook set by setUp()

        // WHEN
        // Use getters
        // EXPECT
        // Getters will show $this->validBook informations
        // can getTitle()
        $this->assertSame('Titre du livre', $this->validBook->getTitle());
        // can getAuthor()
        $this->assertSame('John Doe', $this->validBook->getAuthor());
        // can getImagePath()
        $this->assertSame('/upload/books/book.png', $this->validBook->getImagePath());
        // can getDescription()
        $this->assertSame('Cest un livre', $this->validBook->getDescription());
        // can getAvailability()
        $this->assertSame(BookStatusEnum::AVAILABLE, $this->validBook->getAvailability());
        // can getUsername()
        $this->assertSame('John Doe', $this->validBook->getFromMember()->getUsername());
    }

    public function testSetters(): void
    {
        // GIVEN
        // Have a $this->validBook set by setUp()

        // WHEN
        // Use setters
        $this->validBook->setTitle('Titredulivre2');
        $this->validBook->setAuthor('John Doe2');
        $this->validBook->setImagePath('/upload/books/book2.png');
        $this->validBook->setDescription('Description2');
        $this->validBook->setAvailability(BookStatusEnum::NOTAVAILABLE);
        $this->validBook->setFromMember(new MemberEntity(
            'JohnDoe2',
            'john.doe2@mail.com',
            password_hash('password2', Settings::get(Settings::APP_SECURITY_HASH_ALGO)),
            '/upload/avatars/avatar2.png',
            Locales::getLocalDateTime(),
            Locales::getLocalDateTime(),
            0,
            MemberStatusEnum::VALIDATED
        ));

        // EXPECT
        // Getter will show the same informations
        // can getTitle()
        $this->assertSame('Titredulivre2', $this->validBook->getTitle());
        // can getAuthor()
        $this->assertSame('John Doe2', $this->validBook->getAuthor());
        // can getImagePath()
        $this->assertSame('/upload/books/book2.png', $this->validBook->getImagePath());
        // can getDescription()
        $this->assertSame('Description2', $this->validBook->getDescription());
        // can getAvailability()
        $this->assertSame(BookStatusEnum::NOTAVAILABLE, $this->validBook->getAvailability());
        // can getFromMember()
        $this->assertSame('JohnDoe2', $this->validBook->getFromMember()->getUsername());
    }

    #[TestDox('validateField() return RuntimeException on invalid field at new BookEntity() constructor')]
    public function testValidateFieldReturnExceptionOnInvalidFieldAtBookEntityConstructor()
    {
        // GIVEN
        // Have this informations about a book :
        // 'Titre du livre', 'John Doe', '/upload/books/book.png',
        // 'Cest un livre', BookStatusEnum::AVAILABLE, fromMember 'John Doe'

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an error talking about image
        $this->expectExceptionMessageMatches('/image/');

        // WHEN
        // imagePath is invalid at BookEntity creation
        new BookEntity(
            'Titre du livre',
            'John Doe',
            '/../../../../etc/shadow',
            'Cest un livre',
            BookStatusEnum::AVAILABLE,
            MemberEntityTest::instanciateValidMember(),
        );
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setTitle()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetTitle()
    {
        // GIVEN
        // Have a $this->validBook set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/title must only contain 150 characters in a-z, A-Z, 0-9, _ or -/');

        // WHEN
        // a field is invalid at setTitle()
        $this->validBook->setTitle('Bad+Username');
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setAuthor()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetAuthor()
    {
        // GIVEN
        // Have a $this->validBook set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/author must only contain 150 characters in a-z, A-Z, 0-9, _ or -/');

        // WHEN
        // a field is invalid at setAuthor()
        $this->validBook->setAuthor('Bad+Author');
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setImagePath()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetImagePath()
    {
        // GIVEN
        // Have a $this->validBook set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/imagePath must be stored in \/upload\/books\/,' .
            ' contain only a-z, A-Z or 0-9, and have .png extension/');

        // WHEN
        // a field is invalid at setImagePath()
        $this->validBook->setImagePath('Bad+ImagePath');
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setDescription()')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetDescription()
    {
        // GIVEN
        // Have a $this->validBook set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/description must only contain 150 characters/');

        // WHEN
        // a field is invalid at setDescription()
        $this->validBook->setDescription('Bad+Description');
    }
}
