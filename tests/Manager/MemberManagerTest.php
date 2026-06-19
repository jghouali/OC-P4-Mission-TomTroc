<?php

declare(strict_types=1);

namespace Tests\Manager;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Manager\MemberManager;
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

    #[TestDox('A Users can register as Member')]
    #[TestWith(['John Doe', 'john.doe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testUsersCanRegisterAsMember(string $username, string $email, string $password, string $avatarPath)
    {
        // GIVEN that Manager is up
        $memberManager = new MemberManager();
        // and we have this data from user :
        // $username, $email, $password, $avatarPath

        // WHEN register with it
        $result = $memberManager->register($username, $email, $password, $avatarPath);

        // EXPECT register send True
        $this->assertTrue($result);
        // and member is in database with status NOT-VALIDATED
        $this->assertTrue($memberManager->memberExist($email));
        $this->assertFalse($memberManager->memberExistAndValidated($email));
    }

    #[TestDox('Member can Login')]
    #[TestWith(['John Doe', 'john.doe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testMembersCanLogin(string $username, string $email, string $password, string $avatarPath)
    {
        // GIVEN that Manager is up
        $memberManager = new MemberManager();
        // and we have a member registered :
        $memberManager->register($username, $email, $password, $avatarPath);

        // WHEN member log in
        $result = $memberManager->login($email, $password);

        // EXPECT login send True
        $this->assertTrue($result);
        // and the id, username, avatarPath is present in $_SESSION
        $member = Settings::getMemberRepository()->findByEmail('john.doe@mail.com');
        $this->assertTrue($_SESSION['id'] === $member->getId());
        $this->assertTrue($_SESSION['avatarPath'] === $member->getAvatarPath());
        $this->assertTrue($_SESSION['username'] === $member->getUserName());
    }

    #[TestDox('can see profiles')]
    #[TestWith(['John Doe', 'john.doe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testMembersCanShowProfile(string $username, string $email, string $password, string $avatarPath)
    {
        // GIVEN that Manager is up
        $memberManager = new MemberManager();
        // and we have 2 members registered :
        $memberManager->register($username, $email, $password, $avatarPath);
        $memberManager->register(
            'other profile',
            'other.profile@mail.com',
            'otherpassword',
            '/upload/avatars/otheravatar.png'
        );

        $member = Settings::getMemberRepository()->findByEmail('john.doe@mail.com');
        $member2 = Settings::getMemberRepository()->findByEmail('other.profile@mail.com');

        // WHEN member not logged in
        // id, username, avatarPath is not present in $_SESSION
        $this->assertFalse(isset($_SESSION['id']));
        $this->assertFalse(isset($_SESSION['username']));
        $this->assertFalse(isset($_SESSION['avatarPath']));

        // EXPECT user can see members profiles
        $profileData = $memberManager->getProfileData('John Doe');
        $this->assertSame(
            $member->getId(),
            $profileData['id']
        );
        $this->assertSame(
            $member->getUserName(),
            $profileData['username']
        );
        $this->assertSame(
            $member->getEmail(),
            $profileData['email']
        );
        $this->assertSame(
            $member->getAvatarPath(),
            $profileData['avatarPath']
        );
        $this->assertSame(
            $member->getCreatedAt(),
            $profileData['createdAt']
        );
        $this->assertSame(
            $member->getUpdatedAt(),
            $profileData['updatedAt']
        );
        $this->assertSame(
            $member->getNotificationCount(),
            $profileData['notificationCount']
        );
        $this->assertSame(
            $member->getStatus(),
            $profileData['status']
        );

        $this->assertSame(
            $member2->getId(),
            $memberManager->getProfileData('other profile')['id']
        );
        $this->assertSame(
            $member2->getUserName(),
            $memberManager->getProfileData('other profile')['username']
        );
        $this->assertSame(
            $member2->getEmail(),
            $memberManager->getProfileData('other profile')['email']
        );
        $this->assertSame(
            $member2->getAvatarPath(),
            $memberManager->getProfileData('other profile')['avatarPath']
        );
        $this->assertSame(
            $member2->getCreatedAt(),
            $memberManager->getProfileData('other profile')['createdAt']
        );
        $this->assertSame(
            $member2->getUpdatedAt(),
            $memberManager->getProfileData('other profile')['updatedAt']
        );
        $this->assertSame(
            $member2->getNotificationCount(),
            $memberManager->getProfileData('other profile')['notificationCount']
        );
        $this->assertSame(
            $member2->getStatus(),
            $memberManager->getProfileData('other profile')['status']
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
        // GIVEN that Manager is up
        $memberManager = new MemberManager();
        // and we have a members registered :
        $memberManager->register($username, $email, $password, $avatarPath);

        $member = Settings::getMemberRepository()->findByEmail('john.doe@mail.com');
        // $member is logged in
        $memberManager->login($email, $password);
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
        $result = $memberManager->modifyProfile(
            $member,
            'John Doe2',
            'john.doe2@mail.com',
            'johndoe2',
            '/upload/avatars/johndoe2.png'
        );

        // EXPECT modifyProfile return true
        $this->assertTrue($result);
        // profile is updated
        $member = Settings::getMemberRepository()->findByEmail('john.doe2@mail.com');
        $this->assertSame(
            'John Doe2',
            $memberManager->getProfileData('John Doe2')['username']
        );
        $this->assertSame(
            'john.doe2@mail.com',
            $memberManager->getProfileData('John Doe2')['email']
        );
        $this->assertSame(
            '/upload/avatars/johndoe2.png',
            $memberManager->getProfileData('John Doe2')['avatarPath']
        );
        $this->assertNotSame(
            $memberManager->getProfileData('John Doe2')['createdAt'],
            $memberManager->getProfileData('John Doe2')['updatedAt']
        );
        $this->assertSame(
            $member->getNotificationCount(),
            $memberManager->getProfileData('John Doe2')['notificationCount']
        );
        $this->assertSame(
            $member->getStatus(),
            $memberManager->getProfileData('John Doe2')['status']
        );
    }

    #[TestDox('Logged in Members can not modify other profiles')]
    #[TestWith(['John Doe', 'john.doe@mail.com', 'johndoe', '/upload/avatars/johndoe.png'])]
    public function testLoggedInMembersCanNotModifyOtherProfiles(
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ) {
        // GIVEN that Manager is up
        $memberManager = new MemberManager();
        // and we have 2 members registered :
        $memberManager->register(
            $username,
            $email,
            $password,
            $avatarPath
        );
        $memberManager->register(
            'other profile',
            'other.profile@mail.com',
            'otherpassword',
            '/upload/avatars/otheravatar.png'
        );

        $member = Settings::getMemberRepository()->findByEmail('john.doe@mail.com');
        $member2 = Settings::getMemberRepository()->findByEmail('other.profile@mail.com');
        // $member is logged in
        $memberManager->login($email, $password);
        // id, username, avatarPath is present in $_SESSION
        $this->assertTrue(isset($_SESSION['id']));
        $this->assertTrue(isset($_SESSION['username']));
        $this->assertTrue(isset($_SESSION['avatarPath']));
        $this->assertSame($member->getId(), $_SESSION['id']);
        $this->assertSame($member->getUserName(), $_SESSION['username']);
        $this->assertSame($member->getAvatarPath(), $_SESSION['avatarPath']);

        // WHEN member modify other profile profile
        $result = $memberManager->modifyProfile(
            $member2,
            'John Doe2',
            'john.doe2@mail.com',
            'johndoe2',
            '/upload/avatars/johndoe2.png'
        );

        // EXPECT modifyProfile return false
        $this->assertFalse($result);
    }
}
