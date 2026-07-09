<?php

declare(strict_types=1);

namespace Tests\Manager;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MemberManagerTest extends TestCase
{
    private MemberEntity $member1;
    private MemberEntity $member2;
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
            'john.doe@mail.com',
            'Johndoe2026*',
            '/upload/avatars/johndoe.png'
        );
        $this->member2 = Settings::getAuthentificationService()->register(
            'other profile',
            'other.profile@mail.com',
            'Otherpassword2026*',
            '/upload/avatars/otheravatar.png'
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

    #[TestDox('getProfileData() return an array with valid data')]
    public function testGetMyProfileDataLogged()
    {
        // GIVEN

        // WHEN
        // getMyProfileData()
        $result = Settings::getMemberManager()->getProfileData($this->member1->getId());

        // EXPECT user can see members profiles
        $this->assertSame(
            $this->member1->getUserName(),
            $result->getUsername()
        );
        $this->assertSame(
            $this->member1->getAvatarPath(),
            $result->getAvatarPath()
        );
        $this->assertSame(
            'aujourd\'hui',
            $result->getMemberSince()
        );
        $this->assertSame(
            '0 livre',
            $result->getBookCount()
        );
    }

    #[TestDox('modifyMyProfile() when Logged return valid BookEntity updated')]
    public function testModifyMyProfileLogged()
    {
        // GIVEN
        // We have logged
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'Johndoe2026*');

        // id, username, avatarPath of $member is present in $_SESSION
        $this->assertTrue(Settings::getAuthentificationService()->isLoggedIn());
        $this->assertSame(
            $this->member1->getId(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getId()
        );
        $this->assertSame(
            $this->member1->getUserName(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getUserName()
        );
        $this->assertSame(
            $this->member1->getAvatarPath(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getAvatarPath()
        );

        sleep(1);
        // WHEN member modify his profile
        $result = Settings::getMemberManager()->modifyMyProfile(
            'John Doe2',
            'john.doe2@mail.com',
            'Johndoe22026*',
            '/upload/avatars/johndoe2.png'
        );

        // EXPECT modifyProfile return MemberEntity
        // update $member
        $member = Settings::getMemberRepository()->findOneById($this->member1->getId());
        $this->assertTrue($result::class === 'Green\TomTroc\Entity\MemberEntity');
        $this->assertSame('John Doe2', $member->getUserName());
        $this->assertSame('john.doe2@mail.com', $member->getEmail());
        $this->assertSame('/upload/avatars/johndoe2.png', $member->getAvatarPath());
        $this->assertNotSame($member->getUpdatedAt(), $member->getCreatedAt());
        $this->assertSame($result->getNotificationCount(), $member->getNotificationCount());
        $this->assertSame($result->getStatus(), $member->getStatus());
    }

    #[TestDox('modifyMyProfile() when not Logged throw RuntimeException')]
    public function testModifyMyProfileNotLogged()
    {
        // GIVEN
        // We have logged
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        // EXPECT
        // thrown RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('You are not logged in');

        // WHEN member modify profile
        Settings::getMemberManager()->modifyMyProfile(
            'John Doe2',
            'john.doe2@mail.com',
            'Johndoe22026*',
            '/upload/avatars/johndoe2.png'
        );
    }
}
