<?php

declare(strict_types=1);

namespace Tests\Entity;

use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BookEntityTest extends TestCase
{
    public static string $goodTitle = 'The Kinkfolk Table';
    public static string $goodAuthor = 'Nathan Williams';
    public static string $goodImagePath = '/upload/books/file.png';
    public static string $goodDescription = 'The Kinkfolk Table description';
    public static BookStatusEnum $goodIsRead = BookStatusEnum::AVAILABLE;

    public static array $fieldName = [
        0 => 'title',
        1 => 'author',
        2 => 'imagePath',
        3 => 'description',
        4 => 'availability',
        5 => 'fromMember',
    ];

    public static function generateBookEntityDataProvider(string $label, string $payload): array
    {
        // Get a valid Data set
        $validData = self::validBookDataProvider()['All fields are valid'];

        // For each dataset, one error will be injected into one field at a time
        // except for $availability, because it is an Enum
        // except for $fromMember, because it is a MemberEntity
        // => count($validData) - 2
        for ($i = 0; $i < count($validData) - 2; $i++) {
            $validDataTmp[$i] = [];

            for ($j = 0; $j < count($validData); $j++) {
                // Fill in a valid value
                $validDataTmp[$i][$j] = $validData[$j];

                // Fill in a invalid value
                if ($i === $j) {
                    $validDataTmp[$i][$j] = $payload;

                    // Invalid data on MemberEntity cannot be a string,
                    // so put a GoodMember we will filter the assert
                    if (self::$fieldName[$i] === 'fromMember') {
                        $validDataTmp[$i][$j] = self::createGoodMember();
                    }

                    // enum cannot have data different than enum values
                    // we will filter the assert
                    if (self::$fieldName[$i] === 'availability') {
                        $validDataTmp[$i][$j] = BookStatusEnum::AVAILABLE;
                    }
                }
            }

            // Name the Data Set by his invalid Data
            if (self::$fieldName[$i] === 'availability') {
                $validDataFinal[self::$fieldName[$i] . ' is READ'] = $validDataTmp[$i];
            } else {
                $validDataFinal[self::$fieldName[$i] . " $label"] = $validDataTmp[$i];
            }
        };

        return (
            isset($validDataFinal) &&
            count($validDataFinal) >= 1
        ) ? $validDataFinal : self::validBookDataProvider();
    }

    public static function createGoodMember(): MemberEntity
    {
        return new MemberEntity(
            MemberEntityTest::$goodUsername,
            MemberEntityTest::$goodEmail,
            MemberEntityTest::$goodPasswordHash,
            MemberEntityTest::$goodAvatarPath,
            date_create('1 days ago 12:00'),
            date_create('1 days ago 12:00'),
            MemberEntityTest::$goodNotificationCount,
            MemberEntityTest::$goodStatus,
        );
    }

    public static function validBookDataProvider(): array
    {
        return [
            'All fields are valid' => [
                self::$goodTitle,
                self::$goodAuthor,
                self::$goodImagePath,
                self::$goodDescription,
                BookStatusEnum::AVAILABLE,
                self::createGoodMember(),
            ],
        ];
    }

    public static function invalidDataProvider(): array
    {
        // We inject empty, sql injection and xss invalid field on the Data Set
        $is_empty = self::generateBookEntityDataProvider(
            'is empty',
            ''
        );
        $is_sql_inj = self::generateBookEntityDataProvider(
            '\' OR \'1\'=\'',
            '\' OR \'1\'=\''
        );
        $is_xss = self::generateBookEntityDataProvider(
            '<audio src/onerror=alert(1)>',
            '<audio src/onerror=alert(1)>'
        );

        return array_merge($is_empty, $is_sql_inj, $is_xss);
    }

    public static function getterProvider(): array
    {
        // We take valid Data Set
        $goodBook = self::validBookDataProvider();
        $key = array_key_first($goodBook);
        $value = $goodBook[$key];

        // And derivate it to n*field + the name of each getter and value to test
        $getterArray = [];
        foreach (self::$fieldName as $field) {
            $method = 'get' . substr_replace($field, strtoupper(substr($field, 0, 1)), 0, 1);
            $getterArray[$method] = array_merge([$method, $field], $value);
        }

        return $getterArray;
    }

    public static function setterProvider(): array
    {
        // We take valid Data Set
        $goodBook = self::validBookDataProvider();
        $key = array_key_first($goodBook);
        $value = $goodBook[$key];

        // And derivate it to n*field + the name of each setter and value to test
        $setterArray = [];
        foreach (self::$fieldName as $field) {
            $method = 'set' . substr_replace($field, strtoupper(substr($field, 0, 1)), 0, 1);
            $setterArray[$method] = array_merge([$method, $field], $value);
        }

        return $setterArray;
    }

    #[DataProvider('validBookDataProvider')]
    public function testCanCreateAValidNewBookEntity(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {
        // Given valid informations on create a new BookEntity
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $fromMember,
        );

        // Expected BookEntity is valid

        // A bookEntity is a child of Green\TomTroc\Entity\BookEntity
        $this->assertSame('Green\TomTroc\Entity\BookEntity', get_class($book));

        // It has his title
        $this->assertSame($title, $book->getTitle());

        // It has his author
        $this->assertSame($author, $book->getAuthor());

        // It has his image
        $this->assertSame($imagePath, $book->getImagePath());

        // It has his description
        $this->assertSame($description, $book->getDescription());

        // It has his availibility
        $this->assertSame($availability, $book->getAvailability());

        // It has his FromMember
        $this->assertSame($fromMember, $book->getFromMember());
    }

    #[DataProvider('invalidDataProvider')]
    public function testAnAppropriateExceptionIsThrownWhenInvalidFieldIsPassedForCreateANewBookEntity(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {
        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);

        // When a field is invalid at BookEntity creation
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $fromMember,
        );
    }

    #[DataProvider('validBookDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetTitle(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {
        // When a field is invalid at BookEntity creation
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $fromMember,
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Title must only contain character in a-z, A-Z, 0-9, _ or -/');

        $book->setTitle('Bad+Username');
    }

    #[DataProvider('validBookDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetAuthor(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {
        // When a field is invalid at BookEntity creation
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $fromMember,
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Author must only contain character in a-z, A-Z, 0-9, _ or -/');

        $book->setAuthor('Bad+Author');
    }

    #[DataProvider('validBookDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetImagePath(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {
        // When a field is invalid at BookEntity creation
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $fromMember,
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Book image path must be stored in \/upload\/books\/,' .
            ' contain only a-z, A-Z or 0-9, and have .png extension/');

        $book->setImagePath('Bad+ImagePath');
    }

    #[DataProvider('validBookDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetDescription(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {
        // When a field is invalid at BookEntity creation
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $fromMember,
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Description must only contain character in a-z, A-Z, 0-9, _ or -/');

        $book->setDescription('Bad+Description');
    }

    #[DataProvider('getterProvider')]
    public function testGettersReturnGoodData(
        string $getter,
        string $varName,
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {

        // Given valid informaton about a Book

        // When instanciate a new BookEntity with this informations
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $fromMember,
        );

        // Getter will show the same informations
        $this->assertSame($$varName, $book->$getter());
    }

    #[DataProvider('setterProvider')]
    public function testSettersSetWellData(
        string $setter,
        string $varName,
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability,
        MemberEntity $fromMember,
    ) {
        // Given a bookEntity
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $fromMember,
        );

        $getter = substr_replace($setter, 'g', 0, 1);

        // When a setter change a property, Getter will show the new property
        switch ($varName) {
            case 'availability':
                $book->$setter(BookStatusEnum::NOTAVAILABLE);

                $this->assertEquals(BookStatusEnum::NOTAVAILABLE, $book->$getter());
                break;

            case 'fromMember':
                $book->$setter(self::createGoodMember());

                $this->assertEquals(self::createGoodMember(), $book->$getter());
                break;

            case 'imagePath':
                $book->$setter('/upload/books/newimage.png');

                $this->assertEquals('/upload/books/newimage.png', $book->$getter());
                break;

            default:
                // Validate title, author and description
                $book->$setter('1' . $$varName);

                $this->assertSame('1' . $$varName, $book->$getter());
        }
    }
}
