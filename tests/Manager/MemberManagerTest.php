<?php

declare(strict_types=1);

namespace Tests\Manager;

use Green\TomTroc\Core\Settings\Settings;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MemberManagerTest extends TestCase
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

    #[TestDox('can see profiles')]
    #[TestWith(['John Doe', 'john.doe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testMembersCanShowProfile(string $username, string $email, string $password, string $avatarPath)
    {
        // GIVEN
        // We have 2 members registered :
        $member = Settings::getAuthentificationService()->register($username, $email, $password, $avatarPath);
        $member2 = Settings::getAuthentificationService()->register(
            'other profile',
            'other.profile@mail.com',
            'otherpassword',
            '/upload/avatars/otheravatar.png'
        );

        // WHEN member not logged in
        // id, username, avatarPath is not present in $_SESSION
        $this->assertFalse(isset($_SESSION['id']));
        $this->assertFalse(isset($_SESSION['username']));
        $this->assertFalse(isset($_SESSION['avatarPath']));

        // EXPECT user can see members profiles
        $profileData = Settings::getMemberManager()->getProfileData($member->getId());
        $this->assertSame(
            $member->getUserName(),
            $profileData->getUsername()
        );
        $this->assertSame(
            $member->getAvatarPath(),
            $profileData->getAvatarPath()
        );
        $this->assertSame(
            'aujourd\'hui',
            $profileData->getMemberSince()
        );
        $this->assertSame(
            '0 livre',
            $profileData->getBookCount()
        );

        $profile2Data = Settings::getMemberManager()->getProfileData($member2->getId());
        $this->assertSame(
            $member2->getUserName(),
            $profile2Data->getUsername()
        );
        $this->assertSame(
            $member2->getAvatarPath(),
            $profile2Data->getAvatarPath()
        );
        $this->assertSame(
            'aujourd\'hui',
            $profile2Data->getMemberSince()
        );
        $this->assertSame(
            '0 livre',
            $profile2Data->getBookCount()
        );
    }

    #[TestDox('Logged in Members can modify their profiles')]
    #[TestWith(['John Doe', 'john.doe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testLoggedInMembersCanModifyHisProfile(
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ) {
        // GIVEN
        // and we have a members registered :
        $member = Settings::getAuthentificationService()->register($username, $email, $password, $avatarPath);
        // $member is logged in
        Settings::getAuthentificationService()->login($email, $password);
        // id, username, avatarPath of $member is present in $_SESSION
        $this->assertTrue(isset($_SESSION['id']));
        $this->assertTrue(isset($_SESSION['username']));
        $this->assertTrue(isset($_SESSION['avatarPath']));
        $this->assertSame(
            $member->getId(),
            $_SESSION['id']
        );
        $this->assertSame(
            $member->getUserName(),
            $_SESSION['username']
        );
        $this->assertSame(
            $member->getAvatarPath(),
            $_SESSION['avatarPath']
        );

        sleep(1);
        // WHEN member modify his profile
        $result = Settings::getMemberManager()->modifyMyProfile(
            'John Doe2',
            'john.doe2@mail.com',
            'johndoe2',
            '/upload/avatars/johndoe2.png'
        );

        // EXPECT modifyProfile return MemberEntity
        // update $member
        $member = Settings::getMemberRepository()->findById($member->getId());
        $this->assertTrue($result::class === 'Green\TomTroc\Entity\MemberEntity');
        $this->assertSame('John Doe2', $member->getUserName());
        $this->assertSame('john.doe2@mail.com', $member->getEmail());
        $this->assertSame('/upload/avatars/johndoe2.png', $member->getAvatarPath());
        $this->assertNotSame($member->getUpdatedAt(), $member->getCreatedAt());
        $this->assertSame($result->getNotificationCount(), $member->getNotificationCount());
        $this->assertSame($result->getStatus(), $member->getStatus());
    }
}
