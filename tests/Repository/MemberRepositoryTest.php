<?php

declare(strict_types=1);

namespace Tests\Repository;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
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

    #[TestDox('insert() with valid Given Data return a MemberEntity with the last insert Id')]
    public function testInsertMember(): void
    {
        // GIVEN
        // members table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
        // And have this member
        $member = MemberEntityTest::instanciateValidMember();

        // WHEN
        // insert() it on db
        $result = Settings::getMemberRepository()->insert($member);

        // EXPECT
        // return true
        $this->assertSame('Green\\TomTroc\\Entity\\MemberEntity', $result::class);
        // And there is now 1 row in members table
        $this->assertSame(1, count(Settings::getMemberRepository()->findAll()));
    }

    #[TestDox('insert() with a MemberEntity that already have a valid memberId throw RuntimeException')]
    public function testInsertMemberAlreadyInserted(): void
    {
        // GIVEN
        // members table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
        // And have this member
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);

        // EXPECT
        // throw Runtime Exception
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('This member already inserted');

        // WHEN
        // insert() it on db
        Settings::getMemberRepository()->insert($member);
    }

    #[TestDox('update() with valid given data return a valid BookEntity updated ')]
    public function testUpdate(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
        // and this user exist in db
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('jeanne');
        $member->setEmail('jeanne@mail.com');
        $member = Settings::getMemberRepository()->insert($member);

        $dateFormatted = Locales::getLocalFormattedDateTime('1 days ago 12:00');
        $date = Locales::getLocalDateTime($dateFormatted);

        // WHEN
        // update()
        $member2 = Settings::getMemberRepository()->update(
            $member->getId(),
            new MemberEntity(
                'John Doe',
                'john.doe@mail.com',
                '$2y$12$fbnWIaikClpP97zlHyGxwunusDbEv9Mm5ERF613x8t3zydOJjTq5.',
                '/upload/avatars/johndoe.png',
                $date,
                $date,
                2,
                MemberStatusEnum::NOTVALIDATED
            )
        );

        // EXPECT
        // $member2 is updated
        $this->assertSame($member2->getId(), $member->getId());
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

    #[TestDox('update() with inexistent memberId given throw RuntimeException')]
    public function testUpdateInexistentMemberId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));

        $dateFormatted = Locales::getLocalFormattedDateTime('1 days ago 12:00');
        $date = Locales::getLocalDateTime($dateFormatted);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('memberId doesnt exist');

        // WHEN
        // update()
        $result = Settings::getMemberRepository()->update(
            65,
            new MemberEntity(
                'John Doe',
                'john.doe@mail.com',
                '$2y$12$fbnWIaikClpP97zlHyGxwunusDbEv9Mm5ERF613x8t3zydOJjTq5.',
                '/upload/avatars/johndoe.png',
                $date,
                $date,
                2,
                MemberStatusEnum::NOTVALIDATED
            )
        );
    }

    #[TestDox('update() with memberId mismatch in the given MemberEntity throw RuntimeException')]
    public function testUpdateMismatchMemberId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));

        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('AGoodUsername');
        $member->setEmail('agoodmail@mail.com');
        $member = Settings::getMemberRepository()->insert($member);

        $member2 = MemberEntityTest::instanciateValidMember();
        $member2 = Settings::getMemberRepository()->insert($member2);

        $this->assertNotSame($member->getId(), $member2->getId());

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('memberId mismatch with memberId whithin the given MemberEntity');

        // WHEN
        // update()
        Settings::getMemberRepository()->update(
            $member->getId(),
            $member2
        );
    }

    #[TestDox('delete() return True when successfull')]
    public function testDeleteMember(): void
    {
        // GIVEN
        // members table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
        // And have this member
        $member = MemberEntityTest::instanciateValidMember();
        Settings::getMemberRepository()->insert($member);
        $this->assertSame(1, count(Settings::getMemberRepository()->findAll()));

        // WHEN
        // delete() it
        $result = Settings::getMemberRepository()->delete($member);

        // EXPECT
        // return true
        $this->assertTrue($result);
        // And there is now 0 row in members table
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
    }

    #[TestDox('delete() a member with null memberId throw RuntimeException')]
    public function testDeleteInexistentMemberId(): void
    {
        // GIVEN
        // members table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
        $member = MemberEntityTest::instanciateValidMember();

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('memberId is null');

        // WHEN
        // delete() it
        Settings::getMemberRepository()->delete($member);
    }

    #[TestDox('FindOneById() with an existing memberId return a valid MemberEntity')]
    public function testFindOneById(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);

        // WHEN
        // findOneById()
        $member2 = Settings::getMemberRepository()->findOneById($member->getId());

        // EXPECT
        // return $member informations
        $this->assertSame('Green\\TomTroc\\Entity\\MemberEntity', $member2::class);
        $this->assertSame($member->getId(), $member2->getId());
        $this->assertSame($member->getUserName(), $member2->getUserName());
        $this->assertSame($member->getEmail(), $member2->getEmail());
        $this->assertSame($member->getPasswordHash(), $member2->getPasswordHash());
        $this->assertSame($member->getAvatarPath(), $member2->getAvatarPath());
        $this->assertSame($member->getCreatedAt(), $member2->getCreatedAt());
        $this->assertSame($member->getUpdatedAt(), $member2->getUpdatedAt());
        $this->assertSame($member->getNotificationCount(), $member2->getNotificationCount());
        $this->assertSame($member->getStatus(), $member2->getStatus());
    }

    #[TestDox('FindOneById() with an non-existing memberId return false')]
    public function testFindOneByIdNonExistingMemberId(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));

        // WHEN
        // findOneById()
        $result = Settings::getMemberRepository()->findOneById(775);

        // EXPECT
        // return null
        $this->assertNull($result);
    }

    #[TestDox('FindOneByUsername() with an existing username return a valid MemberEntity')]
    public function testFindOneByUsername(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);

        // WHEN
        // findOneById()
        $member2 = Settings::getMemberRepository()->findOneByUsername($member->getUserName());

        // EXPECT
        // return $member informations
        $this->assertSame('Green\\TomTroc\\Entity\\MemberEntity', $member2::class);
        $this->assertSame($member->getId(), $member2->getId());
        $this->assertSame($member->getUserName(), $member2->getUserName());
        $this->assertSame($member->getEmail(), $member2->getEmail());
        $this->assertSame($member->getPasswordHash(), $member2->getPasswordHash());
        $this->assertSame($member->getAvatarPath(), $member2->getAvatarPath());
        $this->assertSame($member->getCreatedAt(), $member2->getCreatedAt());
        $this->assertSame($member->getUpdatedAt(), $member2->getUpdatedAt());
        $this->assertSame($member->getNotificationCount(), $member2->getNotificationCount());
        $this->assertSame($member->getStatus(), $member2->getStatus());
    }

    #[TestDox('FindOneByUsername() with an non-existing username return false')]
    public function testFindOneByIdNonExistingUsername(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));

        // WHEN
        // findOneById()
        $result = Settings::getMemberRepository()->findOneByUsername('non-existent-username');

        // EXPECT
        // return null
        $this->assertNull($result);
    }

    #[TestDox('FindOneByEmail() with an existing email return a valid MemberEntity')]
    public function testFindOneByEmail(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
        // And have this member in db
        $member = MemberEntityTest::instanciateValidMember();
        $member = Settings::getMemberRepository()->insert($member);

        // WHEN
        // findOneById()
        $member2 = Settings::getMemberRepository()->findOneByEmail($member->getEmail());

        // EXPECT
        // return $member informations
        $this->assertSame('Green\\TomTroc\\Entity\\MemberEntity', $member2::class);
        $this->assertSame($member->getId(), $member2->getId());
        $this->assertSame($member->getUserName(), $member2->getUserName());
        $this->assertSame($member->getEmail(), $member2->getEmail());
        $this->assertSame($member->getPasswordHash(), $member2->getPasswordHash());
        $this->assertSame($member->getAvatarPath(), $member2->getAvatarPath());
        $this->assertSame($member->getCreatedAt(), $member2->getCreatedAt());
        $this->assertSame($member->getUpdatedAt(), $member2->getUpdatedAt());
        $this->assertSame($member->getNotificationCount(), $member2->getNotificationCount());
        $this->assertSame($member->getStatus(), $member2->getStatus());
    }

    #[TestDox('FindOneByEmail() with an non-existing email return false')]
    public function testFindOneByIdNonExistingEmail(): void
    {
        // GIVEN
        // books table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));

        // WHEN
        // findOneById()
        $result = Settings::getMemberRepository()->findOneByEmail('non-existent@mail.com');

        // EXPECT
        // return null
        $this->assertNull($result);
    }

    #[TestDox('FindAll() with N member in db return an array of N number of Member')]
    public function testFindAll(): void
    {
        // GIVEN
        // member table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
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

    #[TestDox('FindAll() with empty db return an array of 0 Member')]
    public function testFindAllEmptyDb(): void
    {
        // GIVEN
        // member table is empty
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));

        // WHEN
        // findAll()
        // EXPECT retieve 2 members
        $this->assertSame(0, count(Settings::getMemberRepository()->findAll()));
    }
}
