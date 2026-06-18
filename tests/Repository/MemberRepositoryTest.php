<?php

declare(strict_types=1);

namespace Tests\Repository;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Enum\MemberStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\MemberEntityTest;

class MemberRepositoryTest extends TestCase
{
    // PHPunit fixtures
    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
        Settings::getMemberRepository()->deleteAll();
    }

    public function setUp(): void
    {
        Settings::getMemberRepository()->deleteAll();
    }

    public function tearDown(): void
    {
        Settings::getMemberRepository()->deleteAll();
    }

    public static function tearDownAfterClass(): void
    {
        Settings::getMemberRepository()->deleteAll();
    }

    #[TestDox('insert() and delete()')]
    public function testInsertAndDeleteMember(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getMemberRepository()->findAll()) === 0);
        // And have this member
        $member = MemberEntityTest::instanciateValidMember();

        // WHEN
        // insert() it on db
        $result = Settings::getMemberRepository()->insert($member);

        // EXPECT
        // return true
        $this->assertTrue($result);
        // And there is now 1 row in members table
        $this->assertSame(1, count(Settings::getMemberRepository()->findAll()));

        // WHEN
        // now, delete() it
        $result2 = Settings::getMemberRepository()->delete($member);

        // EXPECT
        // return true
        $this->assertTrue($result2);
        // And there is now 0 row in members table
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
    }

    #[TestDox('FindAll()')]
    public function testFindAll(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getMemberRepository()->findAll()) === 0);
        // And have these 2 members
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('jean');
        $member->setEmail('jean@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('matthieu');
        $member2->setEmail('matthieu@mail.com');
        // in db
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);

        // WHEN
        // findAll()
        // EXPECT retieve 2 members
        $this->assertSame(2, count(Settings::getMemberRepository()->findAll()));
    }

    #[TestDox('FindAllWhere(\'username\', \'LIKE\', \'%jean%\')')]
    public function testFindAllWhere(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getMemberRepository()->findAll()) === 0);
        // And have these 4 members in db
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('jean');
        $member->setEmail('jean@mail.com');
        Settings::getMemberRepository()->insert($member);

        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('matthieu');
        $member2->setEmail('matthieu@mail.com');
        Settings::getMemberRepository()->insert($member2);

        $member3 = MemberEntityTest::instanciateValidMember();
        $member3->setUserName('jeanne');
        $member3->setEmail('jeanne@mail.com');
        Settings::getMemberRepository()->insert($member3);

        $member4 = MemberEntityTest::instanciateValidMember();
        $member4->setUserName('jeannette');
        $member4->setEmail('jeannette@mail.com');
        Settings::getMemberRepository()->insert($member4);

        $this->assertSame(4, count(Settings::getMemberRepository()->findAll()));

        // WHEN
        // findAllWhere('username', 'LIKE', '%jean%')
        // EXPECT
        // retieve 3 members
        $this->assertSame(3, count(Settings::getMemberRepository()->findAllWhere('username', 'LIKE', '%jean%')));
    }

    #[TestDox('FindAllWhere() return [] with invalid informations')]
    public function testFindAllWhereInvalidData(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getMemberRepository()->findAll()) === 0);
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('jean');
        $member->setEmail('jean@mail.com');
        Settings::getMemberRepository()->insert($member);

        $this->assertSame(1, count(Settings::getMemberRepository()->findAll()));

        // WHEN
        // findAllWhere() with column that do not exist : user LIKE '%jean%'
        // EXPECT
        // retieve 0 member  // this comportement may change
        $this->assertSame(0, count(Settings::getMemberRepository()->findAllWhere('user', 'LIKE', '%jean%')));

        // WHEN
        // findAllWhere() with operator
        // not in ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'] : username + '%jean%'
        // EXPECT
        // retieve 0 member  // this comportement change change
        $this->assertSame(0, count(Settings::getMemberRepository()->findAllWhere('username', '+', '%jean%')));
    }

    #[TestDox('FindById()')]
    public function testFindById(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getMemberRepository()->findAll()) === 0);
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);

        // WHEN
        // findById()
        $member2 = Settings::getMemberRepository()->findById($member->getId());

        // EXPECT
        // return $member informations
        $this->assertSame($member->getUserName(), $member2->getUserName());
        $this->assertSame($member->getEmail(), $member2->getEmail());
        $this->assertSame($member->getPasswordHash(), $member2->getPasswordHash());
        $this->assertSame($member->getAvatarPath(), $member2->getAvatarPath());
        $this->assertSame($member->getCreatedAt(), $member2->getCreatedAt());
        $this->assertSame($member->getUpdatedAt(), $member2->getUpdatedAt());
        $this->assertSame($member->getNotificationCount(), $member2->getNotificationCount());
        $this->assertSame($member->getStatus(), $member2->getStatus());
    }

    #[TestDox('FindByUsername()')]
    public function testFindByUsername(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getMemberRepository()->findAll()) === 0);
        // And we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);

        // WHEN
        // findByEmail()
        $member2 = Settings::getMemberRepository()->findByUsername($member->getUserName());

        // EXPECT
        // return $member informations
        $this->assertSame($member->getUserName(), $member2->getUserName());
        $this->assertSame($member->getEmail(), $member2->getEmail());
        $this->assertSame($member->getPasswordHash(), $member2->getPasswordHash());
        $this->assertSame($member->getAvatarPath(), $member2->getAvatarPath());
        $this->assertSame($member->getCreatedAt(), $member2->getCreatedAt());
        $this->assertSame($member->getUpdatedAt(), $member2->getUpdatedAt());
        $this->assertSame($member->getNotificationCount(), $member2->getNotificationCount());
        $this->assertSame($member->getStatus(), $member2->getStatus());
    }

    #[TestDox('FindByEmail()')]
    public function testFindByEmail(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getMemberRepository()->findAll()) === 0);
        // And we have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);

        // WHEN
        // findByEmail()
        $member2 = Settings::getMemberRepository()->findByEmail($member->getEmail());

        // EXPECT
        // return $member informations
        $this->assertSame($member->getUserName(), $member2->getUserName());
        $this->assertSame($member->getEmail(), $member2->getEmail());
        $this->assertSame($member->getPasswordHash(), $member2->getPasswordHash());
        $this->assertSame($member->getAvatarPath(), $member2->getAvatarPath());
        $this->assertSame($member->getCreatedAt(), $member2->getCreatedAt());
        $this->assertSame($member->getUpdatedAt(), $member2->getUpdatedAt());
        $this->assertSame($member->getNotificationCount(), $member2->getNotificationCount());
        $this->assertSame($member->getStatus(), $member2->getStatus());
    }

    #[TestDox('update()')]
    public function testUpdate(): void
    {
        // GIVEN
        // books table is empty
        $this->assertTrue(count(Settings::getMemberRepository()->findAll()) === 0);
        // and this user exist in db
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('jeanne');
        $member->setEmail('jeanne@mail.com');
        Settings::getMemberRepository()->insert($member);

        // WHEN
        // update()
        $member2 = Settings::getMemberRepository()->findByEmail($member->getEmail());
        $dateFormatted = Locales::getLocalFormattedDateTime('1 days ago 12:00');
        $date = Locales::getLocalDateTime($dateFormatted);
        $member2->setUserName('John Doe');
        $member2->setEmail('john.doe@mail.com');
        $member2->setPasswordHash('$2y$12$fbnWIaikClpP97zlHyGxwunusDbEv9Mm5ERF613x8t3zydOJjTq5.');
        $member2->setAvatarPath('/upload/avatars/johndoe.png');
        $member2->setCreatedAt($date);
        $member2->setUpdatedAt($date);
        $member2->setNotificationCount(2);
        $member2->setStatus(MemberStatusEnum::NOTVALIDATED);
        Settings::getMemberRepository()->update($member2->getId(), $member2);

        // EXPECT
        // $member2 is updated
        $this->assertSame('John Doe', $member2->getUserName());
        $this->assertSame('john.doe@mail.com', $member2->getEmail());
        $this->assertSame('$2y$12$fbnWIaikClpP97zlHyGxwunusDbEv9Mm5ERF613x8t3zydOJjTq5.', $member2->getPasswordHash());
        $this->assertSame('/upload/avatars/johndoe.png', $member2->getAvatarPath());
        $this->assertSame($dateFormatted, $member2->getCreatedAt());
        // updatedAt is updated at now
        $this->assertSame(Locales::getLocalFormattedDateTime(), $member2->getUpdatedAt());
        $this->assertSame(2, $member2->getNotificationCount());
        $this->assertSame(MemberStatusEnum::NOTVALIDATED, $member2->getStatus());
    }
}
