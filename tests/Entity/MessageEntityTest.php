<?php

declare(strict_types=1);

namespace Tests\Entity;

use DateTime;
use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MessageEntityTest extends TestCase
{
    private MessageEntity $validMessage;
    private static string $dateFormatted;
    private static DateTime $date;

    // PHPunit fixtures
    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
    }

    public function tearDown(): void
    {
        unset($this->validMember);
    }

    public function setUp(): void
    {
        self::$dateFormatted = Locales::getLocalFormattedDateTime();
        self::$date = Locales::getLocalDateTime(self::$dateFormatted);
        $this->validMessage = self::instanciateValidMessage();
    }

    public static function instanciateValidMessage(): MessageEntity
    {
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('Jacques');
        $member2->setEmail('jean-jacques@mail.com');

        self::$dateFormatted = Locales::getLocalFormattedDateTime();
        self::$date = Locales::getLocalDateTime(self::$dateFormatted);

        return new MessageEntity(
            'Hello',
            self::$date,
            self::$date,
            MemberEntityTest::instanciateValidMember(),
            $member2,
            MessageStatusEnum::NOTREAD,
        );
    }

    public function testMessageEntityConstructor()
    {
        // GIVEN
        // Have John Doe send a message "hello" to Jacques :
        $member1 = MemberEntityTest::instanciateValidMember();
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('Jacques');
        $member2->setEmail('jacques@mail.com');

        // WHEN
        // Create MessageEntity using constructor
        $message = new MessageEntity(
            'Hello',
            self::$date,
            self::$date,
            $member1,
            $member2,
            MessageStatusEnum::NOTREAD,
        );

        // EXPECT
        // $message is a child of Green\TomTroc\Entity\MessageEntity
        $this->assertSame('Green\TomTroc\Entity\MessageEntity', get_class($message));
        // can getContent()
        $this->assertSame('Hello', $message->getContent());
        // can getIsRead()
        $this->assertSame(MessageStatusEnum::NOTREAD, $message->getIsRead());
        // can getSentAt()
        $this->assertSame(self::$date, $message->getSentAt());
        // can getModifiedAt()
        $this->assertSame(self::$date, $message->getModifiedAt());
        // can getFromMember()
        $this->assertSame($member1->getUserName(), $message->getFromMember()->getUserName());
        // can getToMember()
        $this->assertSame($member2->getUserName(), $message->getToMember()->getUserName());
    }

    public function testGetters()
    {
        // GIVEN
        // Have a $this->validMessage set by setUp()

        // WHEN
        // Use getters
        // EXPECT
        // Show $this->validMessage informations
        $this->assertSame('Hello', $this->validMessage->getContent());
        $this->assertSame(self::$date, $this->validMessage->getSentAt());
        $this->assertSame(self::$date, $this->validMessage->getModifiedAt());
        $this->assertSame('John Doe', $this->validMessage->getFromMember()->getUsername());
        $this->assertSame('Jacques', $this->validMessage->getToMember()->getUsername());
        $this->assertSame(MessageStatusEnum::NOTREAD, $this->validMessage->getIsRead());
    }

    public function testSetters()
    {
        // GIVEN
        // Have a $this->validMessage set by setUp()

        // WHEN
        // Use setters
        $this->validMessage->setContent('NEW');
        $this->validMessage->setSentAt(Locales::getLocalDateTime('yesterday 12:00'));
        $this->validMessage->setModifiedAt(Locales::getLocalDateTime('yesterday 12:00'));
        $this->validMessage->setIsRead(MessageStatusEnum::READ);

        $member1 = MemberEntityTest::instanciateValidMember();
        $member1->setUserName('Georges');
        $member1->setEmail('georges@mail.com');
        $this->validMessage->setFromMember($member1);

        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('Matthieu');
        $member2->setEmail('matthieu@mail.com');
        $this->validMessage->setToMember($member2);

        // EXPECT
        // Show $this->validMessage informations
        $this->assertSame('NEW', $this->validMessage->getContent());
        $this->assertSame(
            Locales::getLocalFormattedDateTime('yesterday 12:00'),
            date_format($this->validMessage->getSentAt(), 'Y-m-d H:i:s')
        );
        $this->assertSame(
            Locales::getLocalFormattedDateTime('yesterday 12:00'),
            date_format($this->validMessage->getModifiedAt(), 'Y-m-d H:i:s')
        );
        $this->assertSame(MessageStatusEnum::READ, $this->validMessage->getIsRead());
        $this->assertSame('Georges', $this->validMessage->getFromMember()->getUserName());
        $this->assertSame('Matthieu', $this->validMessage->getToMember()->getUserName());
    }

    #[TestDox('validateField() return RuntimeException on invalid field at new MessageEntity() constructor')]
    public function testValidateFieldReturnExceptionOnInvalidFieldAtMessageEntityConstructor()
    {
        // GIVEN
        // Have John Doe send a message "hello" to Jacques :
        $member1 = MemberEntityTest::instanciateValidMember();
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('Jacques');
        $member2->setEmail('jean-jacques@mail.com');

        self::$dateFormatted = Locales::getLocalFormattedDateTime();
        self::$date = Locales::getLocalDateTime(self::$dateFormatted);

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);

        // WHEN
        // content is invalid at MemberEntity creation
        $message = new MessageEntity(
            'Hello <script XSS Attack of the Death>',
            self::$date,
            self::$date,
            $member1,
            $member2,
            MessageStatusEnum::NOTREAD,
        );
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setContent()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetContent()
    {
        // GIVEN
        // Have a $this->validMessage set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/content must only contain characters/');

        // WHEN
        // a field is invalid at setContent()
        $this->validMessage->setContent('Bad+Username');
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setSentAt()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetSentAt()
    {
        // GIVEN
        // Have a $this->validMessage set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/sentAt must be before now and afer 110 years ago/');

        // WHEN create the message entity, with invalid data
        $this->validMessage->setSentAt(Locales::getLocalDateTime('200 year ago'));
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setModifiedAt()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetModifiedAt()
    {
        // GIVEN
        // Have a $this->validMessage set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/modifiedAt must be before now and afer 110 years ago/');

        // WHEN create the message entity, with invalid data
        $this->validMessage->setModifiedAt(Locales::getLocalDateTime('200 year ago'));
    }
}
