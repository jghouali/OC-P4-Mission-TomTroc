<?php

declare(strict_types=1);

namespace Tests\Entity;

use DateTime;
use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MemberEntityTest extends TestCase
{
    private MemberEntity $validMember;
    private static string $dateFormatted;
    private static DateTime $date;

    // PHPunit fixtures
    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
    }

    public function setUp(): void
    {
        self::$dateFormatted = Locales::getLocalFormattedDateTime();
        self::$date = Locales::getLocalDateTime(self::$dateFormatted);
        $this->validMember = self::instanciateValidMember();
    }

    public function tearDown(): void
    {
        unset($this->validMember);
    }

    public static function instanciateValidMember(): MemberEntity
    {
        self::$dateFormatted = Locales::getLocalFormattedDateTime();
        self::$date = Locales::getLocalDateTime(self::$dateFormatted);
        return new MemberEntity(
            'John Doe',
            'john.doe@mail.com',
            password_hash('password', Settings::get(Settings::APP_SECURITY_HASH_ALGO)),
            '/upload/avatars/avatar.png',
            self::$date,
            self::$date,
            0,
            MemberStatusEnum::VALIDATED
        );
    }

    public function testMemberEntityConstructor()
    {
        // GIVEN
        // Have this information about a member :
        // 'John Doe', 'john.goe@mail.com', 'password', '/upload/avatars/avatar.png',
        // self::$date, self::$date, 0, MemberStatusEnum::VALIDATED

        // WHEN
        // Create MemberEntity using constructor
        $member = new MemberEntity(
            'John Doe',
            'john.goe@mail.com',
            password_hash('password', Settings::get(Settings::APP_SECURITY_HASH_ALGO)),
            '/upload/avatars/avatar.png',
            self::$date,
            self::$date,
            0,
            MemberStatusEnum::VALIDATED
        );

        // EXPECT
        // $member is a child of Green\TomTroc\Entity\MemberEntity
        $this->assertSame('Green\TomTroc\Entity\MemberEntity', get_class($member));
        // can getUsername()
        $this->assertSame('John Doe', $member->getUsername());
        // can getEmail()
        $this->assertSame('john.goe@mail.com', $member->getEmail());
        // can getPasswordHash()
        $this->assertTrue(password_verify('password', $member->getPasswordHash()));
        // can getAvatarPath()
        $this->assertSame('/upload/avatars/avatar.png', $member->getAvatarPath());
        // can getCreatedAt()
        $this->assertSame(self::$dateFormatted, $member->getCreatedAt());
        // can getUpdatedAt()
        $this->assertSame(self::$dateFormatted, $member->getUpdatedAt());
        // can getStatus()
        $this->assertSame(MemberStatusEnum::VALIDATED, $member->getStatus());
    }

    public function testGetters()
    {
        // GIVEN
        // Have $this->validMember set by setUp()

        // WHEN
        // Use getters
        // EXPECT
        // Getters will show $this->validMember informations
        $this->assertSame('John Doe', $this->validMember->getUserName());
        $this->assertSame('john.doe@mail.com', $this->validMember->getEmail());
        $this->assertTrue(password_verify('password', $this->validMember->getPasswordHash()));
        $this->assertSame('/upload/avatars/avatar.png', $this->validMember->getAvatarPath());
        $this->assertSame(self::$dateFormatted, $this->validMember->getCreatedAt());
        $this->assertSame(self::$dateFormatted, $this->validMember->getUpdatedAt());
        $this->assertSame(0, $this->validMember->getNotificationCount());
        $this->assertSame(MemberStatusEnum::VALIDATED, $this->validMember->getStatus());
    }

    public function testSetters()
    {
        // GIVEN
        // Have $this->validMember set by setUp()

        // WHEN
        // Use setters
        $this->validMember->setUserName('John Doedoe');
        $this->validMember->setEmail('john.doedoe@mail.com');
        $this->validMember->setPasswordHash(
            password_hash('newpassword', Settings::get(Settings::APP_SECURITY_HASH_ALGO))
        );
        $this->validMember->setAvatarPath('/upload/avatars/newavatar.png');
        $this->validMember->setCreatedAt(self::$date);
        $this->validMember->setUpdatedAt(self::$date);
        $this->validMember->setNotificationCount(10);
        $this->validMember->setStatus(MemberStatusEnum::VALIDATED);

        // EXPECT
        // Getters will show the same informations
        $this->assertSame('John Doedoe', $this->validMember->getUserName());
        $this->assertSame('john.doedoe@mail.com', $this->validMember->getEmail());
        $this->assertTrue(password_verify('newpassword', $this->validMember->getPasswordHash()));
        $this->assertSame('/upload/avatars/newavatar.png', $this->validMember->getAvatarPath());
        $this->assertSame(self::$dateFormatted, $this->validMember->getCreatedAt());
        $this->assertSame(self::$dateFormatted, $this->validMember->getUpdatedAt());
        $this->assertSame(10, $this->validMember->getNotificationCount());
        $this->assertSame(MemberStatusEnum::VALIDATED, $this->validMember->getStatus());
    }

    #[TestDox('validateField() return RuntimeException on invalid field at new MemberEntity() constructor')]
    public function testValidateFieldReturnExceptionOnInvalidFieldAtMemberEntityConstructor()
    {
        // GIVEN
        // Have this information about a member :
        // 'John Doe', 'john.goe@mail.com', 'password', '/upload/avatars/avatar.png',
        // self::$date, self::$date, 0, MemberStatusEnum::VALIDATED

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);

        // WHEN
        // avatarPath is invalid at MemberEntity creation
        $member = new MemberEntity(
            'JohnDoe',
            'john.goe@mail.com',
            password_hash('password', Settings::get(Settings::APP_SECURITY_HASH_ALGO)),
            '/../../../etc/shadow',
            self::$date,
            self::$date,
            0,
            MemberStatusEnum::VALIDATED
        );
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setUsername()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetUsername()
    {
        // GIVEN
        // Have a $this->validMember set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/username must only contain 50 readable characters/');

        // WHEN
        // a field is invalid at setUserName()
        $this->validMember->setUserName("Bad\x00Username");
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setEmail()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetEmail()
    {
        // GIVEN
        // Have a $this->validMember set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/email is not a valid email/');

        // WHEN
        // a field is invalid at setEmail()
        $this->validMember->setEmail('Bad@@.com');
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setPasswordHash()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetPasswordHash()
    {
        // GIVEN we have a $this->validMember set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/passwordHash is not a valid bcrypt hash/');

        // WHEN
        // a field is invalid at setPasswordHash()
        $this->validMember->setPasswordHash('Bad+Password');
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setAvatarPath()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetAvatarPath()
    {
        // GIVEN we have a $this->validMember set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/avatarPath must be stored in \/upload\/avatars\/,' .
            ' contain only a-z, A-Z or 0-9, and have .png extension/');

        // WHEN
        // a field is invalid at setAvatarPath()
        $this->validMember->setAvatarPath('/../../../passwd');
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setCreatedAt()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetCreatedAt()
    {
        // GIVEN we have a $this->validMember set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/createdAt must be before now and afer 110 years ago/');

        // WHEN
        // a field is invalid at setCreatedAt()
        $this->validMember->setCreatedAt(new DateTime('200 year ago'));
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setUpdatedAt()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetUpdatedAt()
    {
        // GIVEN we have a $this->validMember set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/updatedAt must be before now and afer 110 years ago/');

        // WHEN
        // a field is invalid at setUpdatedAt()
        $this->validMember->setUpdatedAt(new DateTime('200 year ago'));
    }

    #[TestDox('validateField() return RuntimeException on invalid field on setNotificationCount()')]
    public function testValidateFieldReturnExceptionOnInvalidFieldOnSetNotificationCount()
    {
        // GIVEN we have a $this->validMember set by setUp()

        // EXPECT
        // Have a RuntimeException
        $this->expectException(RuntimeException::class);
        // And an appropriate error message
        $this->expectExceptionMessageMatches('/notificationCount must be >= 0/');

        // WHEN
        // a field is invalid at setNotificationCount()
        $this->validMember->setNotificationCount(-2);
    }
}
