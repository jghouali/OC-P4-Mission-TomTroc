<?php

declare(strict_types=1);

namespace Tests\Entity;

use DateTime;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MessageEntityTest extends TestCase
{
    public static string $goodContent = 'Hello';
    public static MessageStatusEnum $goodIsRead = MessageStatusEnum::READ;

    public static array $fieldName = [
        0 => 'content',
        1 => 'sentAt',
        2 => 'modifiedAt',
        3 => 'fromMember',
        4 => 'toMember',
        5 => 'isRead',
    ];

    public static function generateMessageEntityDataProvider(string $label, string $payload): array
    {
        // Get a valid Data set
        $validData = self::validMessageDataProvider()['All fields are valid'];

        // For each dataset, one error will be injected into one field at a time
        // except for $isRead, because it is an Enum
        // except for $fromMember, because it is a MemberEntity
        // except for $toMember, because it is a MemberEntity
        // => count($validData) - 3
        for ($i = 0; $i < count($validData) - 3; $i++) {
            $validDataTmp[$i] = [];

            for ($j = 0; $j < count($validData); $j++) {
                // Fill in a valid value
                $validDataTmp[$i][$j] = $validData[$j];

                // Fill in a invalid value
                if ($i === $j) {
                    $validDataTmp[$i][$j] = $payload;

                    // Invalid data on DateTime cannot be a string, so put a 200 years ago DateTime
                    if (self::$fieldName[$i] === 'sentAt') {
                        $validDataTmp[$i][$j] = date_create('200 years ago');
                    }

                    // Invalid data on Datetime cannot be a string, so put a 200 years ago DateTime
                    if (self::$fieldName[$i] === 'modifiedAt') {
                        $validDataTmp[$i][$j] = date_create('200 years ago');
                    }

                    // Invalid data on MemberEntity cannot be a string,
                    // so put a GoodMember we will filter the assert
                    if (self::$fieldName[$i] === 'fromMember') {
                        $validDataTmp[$i][$j] = self::createGoodMember();
                    }

                    // Invalid data on MemberEntity cannot be a string,
                    // so put a GoodMember we will filter the assert
                    if (self::$fieldName[$i] === 'toMember') {
                        $validDataTmp[$i][$j] = self::createGoodMember();
                    }

                    // enum cannot have data different than enum values
                    // we will filter the assert
                    if (self::$fieldName[$i] === 'isRead') {
                        $validDataTmp[$i][$j] = MessageStatusEnum::READ;
                    }
                }
            }

            // Name the Data Set by his invalid Data
            if (self::$fieldName[$i] === 'sentAt') {
                $validDataFinal[self::$fieldName[$i] . ' is 200 years ago'] = $validDataTmp[$i];
            } elseif (self::$fieldName[$i] === 'modifiedAt') {
                $validDataFinal[self::$fieldName[$i] . ' is 200 years ago'] = $validDataTmp[$i];
            } elseif (self::$fieldName[$i] === 'isRead') {
                $validDataFinal[self::$fieldName[$i] . ' is READ'] = $validDataTmp[$i];
            } else {
                $validDataFinal[self::$fieldName[$i] . " $label"] = $validDataTmp[$i];
            }
        };

        return (
            isset($validDataFinal) &&
            count($validDataFinal) >= 1
        ) ? $validDataFinal : self::validMessageDataProvider();
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

    public static function validMessageDataProvider(): array
    {
        return [
            'All fields are valid' => [
                self::$goodContent,
                date_create('now'),
                date_create('now'),
                self::createGoodMember(),
                self::createGoodMember(),
                self::$goodIsRead,
            ],
        ];
    }

    public static function invalidDataProvider(): array
    {
        // We inject empty, sql injection and xss invalid field on the Data Set
        $is_empty = self::generateMessageEntityDataProvider(
            'is empty',
            ''
        );
        $is_sql_inj = self::generateMessageEntityDataProvider(
            '\' OR \'1\'=\'',
            '\' OR \'1\'=\''
        );
        $is_xss = self::generateMessageEntityDataProvider(
            '<audio src/onerror=alert(1)>',
            '<audio src/onerror=alert(1)>'
        );

        return array_merge($is_empty, $is_sql_inj, $is_xss);
    }

    public static function getterProvider(): array
    {
        // We take valid Data Set
        $goodMessage = self::validMessageDataProvider();
        $key = array_key_first($goodMessage);
        $value = $goodMessage[$key];

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
        $goodMessage = self::validMessageDataProvider();
        $key = array_key_first($goodMessage);
        $value = $goodMessage[$key];

        // And derivate it to n*field + the name of each setter and value to test
        $setterArray = [];
        foreach (self::$fieldName as $field) {
            $method = 'set' . substr_replace($field, strtoupper(substr($field, 0, 1)), 0, 1);
            $setterArray[$method] = array_merge([$method, $field], $value);
        }

        return $setterArray;
    }

    #[DataProvider('validMessageDataProvider')]
    public function testCanCreateAValidNewMessageEntity(
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {
        // Given valid informations on create a new MessageEntity
        $message = new MessageEntity(
            $content,
            $sentAt,
            $modifiedAt,
            $fromMember,
            $toMember,
            $isRead,
        );

        // Expected MessageEntity is valid

        // A messageEntity is a child of Green\TomTroc\Entity\MessageEntity
        $this->assertSame('Green\TomTroc\Entity\MessageEntity', get_class($message));

        // It has his content
        $this->assertSame($content, $message->getContent());

        // It has his isRead
        $this->assertSame($isRead, $message->getIsRead());

        // It has his sentAt, representing the date of sending
        $this->assertSame($sentAt, $message->getSentAt());

        // It has his modifiedAt, representing the date of modify
        $this->assertSame($modifiedAt, $message->getModifiedAt());

        // It has his From Member
        $this->assertSame($fromMember, $message->getFromMember());

        // It has his To Member
        $this->assertSame($toMember, $message->getToMember());
    }

    #[DataProvider('invalidDataProvider')]
    public function testAnAppropriateExceptionIsThrownWhenInvalidFieldIsPassedForCreateANewMessageEntity(
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {
        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);

        // When a field is invalid at MessageEntity creation
        $message = new MessageEntity(
            $content,
            $sentAt,
            $modifiedAt,
            $fromMember,
            $toMember,
            $isRead,
        );
    }

    #[DataProvider('validMessageDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetContent(
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {
        // When a field is invalid at MessageEntity creation
        $message = new MessageEntity(
            $content,
            $sentAt,
            $modifiedAt,
            $fromMember,
            $toMember,
            $isRead,
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Content must only contain character in a-z, A-Z, 0-9, _ or -/');

        $message->setContent('Bad+Username');
    }

    #[DataProvider('validMessageDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetSentAt(
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {
        // When a field is invalid at MessageEntity creation
        $message = new MessageEntity(
            $content,
            $sentAt,
            $modifiedAt,
            $fromMember,
            $toMember,
            $isRead,
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Sent Date must be before now and afer 110 years ago/');

        $message->setSentAt(date_create('200 year ago'));
    }

    #[DataProvider('validMessageDataProvider')]
    public function testAnAppropriateExceptionIsThrownByValidateFieldOnSetModifiedAt(
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {
        // When a field is invalid at MessageEntity creation
        $message = new MessageEntity(
            $content,
            $sentAt,
            $modifiedAt,
            $fromMember,
            $toMember,
            $isRead,
        );

        // We expect a RuntimeException
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Modified Date must be before now and afer 110 years ago/');

        $message->setModifiedAt(date_create('200 year ago'));
    }

    #[DataProvider('getterProvider')]
    public function testGettersReturnGoodData(
        string $getter,
        string $varName,
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {

        // Given valid informaton about a Message

        // When instanciate a new MessageEntity with this informations
        $message = new MessageEntity(
            $content,
            $sentAt,
            $modifiedAt,
            $fromMember,
            $toMember,
            $isRead,
        );

        // Getter will show the same informations
        $this->assertSame($$varName, $message->$getter());
    }

    #[DataProvider('setterProvider')]
    public function testSettersSetWellData(
        string $setter,
        string $varName,
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {
        // Given a messageEntity
        $message = new MessageEntity(
            $content,
            $sentAt,
            $modifiedAt,
            $fromMember,
            $toMember,
            $isRead,
        );

        $getter = substr_replace($setter, 'g', 0, 1);

        // When a setter change a property, Getter will show the new property
        switch ($varName) {
            case 'sentAt':
                $message->$setter(date_create('yesterday'));

                $this->assertEquals(date_create('yesterday'), $message->$getter());
                break;

            case 'modifiedAt':
                $message->$setter(date_create('yesterday'));

                $this->assertEquals(date_create('yesterday'), $message->$getter());
                break;

            case 'isRead':
                $message->$setter(MessageStatusEnum::NOTREAD);

                $this->assertEquals(MessageStatusEnum::NOTREAD, $message->$getter());
                break;

            case 'fromMember':
                $message->$setter(self::createGoodMember());

                $this->assertEquals(self::createGoodMember(), $message->$getter());
                break;

            case 'toMember':
                $message->$setter(self::createGoodMember());

                $this->assertEquals(self::createGoodMember(), $message->$getter());
                break;

            default:
                // Validate content
                $message->$setter('1' . $$varName);

                $this->assertSame('1' . $$varName, $message->$getter());
        }
    }
}
