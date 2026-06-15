<?php

declare(strict_types=1);

namespace Tests\Repository;

use DateTime;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use Green\TomTroc\Repository\MemberRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\MemberEntityTest;

class MemberRepositoryTest extends TestCase
{
    private static MemberRepository $memberRepository;

    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();

        self::$memberRepository = new MemberRepository();
    }

    public function setUp(): void
    {
        self::$memberRepository->deleteAll();
    }

    public function tearDown(): void
    {
        self::$memberRepository->deleteAll();
    }

    public static function tearDownAfterClass(): void
    {
        self::$memberRepository->deleteAll();
    }

    public function createMember(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount
    ) {
        return new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatarPath,
            $createdAt,
            $updatedAt,
            $notificationCount,
            MemberStatusEnum::VALIDATED
        );
    }

    public static function createGoodMemberDataProvider(): array
    {
        return [
            'GoodMember' => [
                new MemberEntity(
                    MemberEntityTest::$goodUsername,
                    MemberEntityTest::$goodEmail,
                    MemberEntityTest::$goodPasswordHash,
                    MemberEntityTest::$goodAvatarPath,
                    date_create('1 days ago 12:00'),
                    date_create('1 days ago 12:00'),
                    MemberEntityTest::$goodNotificationCount,
                    MemberEntityTest::$goodStatus,
                ),
            ],
        ];
    }

    #[TestDox('Save a Member with valid data and delete it')]
    public function testCanSaveAndDeleteAMember()
    {
        // GIVEN that table members is empty
        $this->assertTrue(count(self::$memberRepository->findAll()) === 0);

        // and this member
        $date = date_create('1 days ago 12:00');
        $member = new MemberEntity(
            'Jean',
            'jean@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            $date,
            $date,
            0,
            MemberStatusEnum::NOTVALIDATED
        );

        // WHEN we insert it
        self::$memberRepository->insert($member);

        // EXPECT it is stored in db and there is now one row in table members
        $this->assertTrue(count(self::$memberRepository->findAll()) === 1);

        // WHEN now, we delete it
        self::$memberRepository->delete($member);

        // EXPECT there is 0 row in member table
        $this->assertTrue(count(self::$memberRepository->findAll()) === 0);
    }

    #[TestDox('FindByEmail and ensure getters gives the correct data')]
    public function testFindByEmail()
    {
        // GIVEN that table members is empty
        $this->assertTrue(count(self::$memberRepository->findAll()) === 0);
        // and we have this member entity
        $date = date_create('1 days ago 12:00');
        $member = new MemberEntity(
            'Jean',
            'jean@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            $date,
            $date,
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        // stored in db
        self::$memberRepository->insert($member);

        // WHEN we search for it with findByEmail
        $member2 = self::$memberRepository->findByEmail($member->getEmail());

        // EXPECT all data retrieve are good
        $this->assertSame($member->getUserName(), $member2->getUserName());
        $this->assertSame($member->getEmail(), $member2->getEmail());
        $this->assertSame($member->getPasswordHash(), $member2->getPasswordHash());
        $this->assertSame($member->getAvatarPath(), $member2->getAvatarPath());
        $this->assertSame(
            $member->getCreatedAt()->format('Y-m-d H:i:s'),
            $member2->getCreatedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame(
            $member->getUpdatedAt()->format('Y-m-d H:i:s'),
            $member2->getUpdatedAt()->format('Y-m-d H:i:s')
        );
        $this->assertSame($member->getNotificationCount(), $member2->getNotificationCount());
        $this->assertSame($member->getStatus(), $member2->getStatus());
    }

    #[TestDox('FindAllWhere can filter members by email, username, and return the exact count of members')]
    public function testFindAllWhere()
    {
        // GIVEN that table members is empty
        $this->assertTrue(count(self::$memberRepository->findAll()) === 0);
        // and we have 4 members with this data, then we stored them in db
        [$member] = self::createGoodMemberDataProvider()['GoodMember'];
        $member->setUserName('jean');
        $member->setEmail('jean@mail.com');
        self::$memberRepository->insert($member);

        [$member2] = self::createGoodMemberDataProvider()['GoodMember'];
        $member2->setUserName('matthieu');
        $member2->setEmail('matthieu@mail.com');
        self::$memberRepository->insert($member2);

        [$member3] = self::createGoodMemberDataProvider()['GoodMember'];
        $member3->setUserName('jeanne');
        $member3->setEmail('jeanne@mail.com');
        self::$memberRepository->insert($member3);

        [$member4] = self::createGoodMemberDataProvider()['GoodMember'];
        $member4->setUserName('jeannette');
        $member4->setEmail('jeannette@mail.com');
        self::$memberRepository->insert($member4);

        // GIVEN members are on db
        $this->assertSame(4, count(self::$memberRepository->findAll()));

        // WHEN we use findAllWhere() with : username LIKE '%jean%'
        // EXPECT we retieve 3 members
        $this->assertSame(3, count(self::$memberRepository->findAllWhere('username', 'LIKE', '%jean%')));
    }

    #[TestDox('FindAllWhere cannot give an inexisting column')]
    public function testFindAllWhereInexistingColumn()
    {
        // GIVEN that table members is empty
        $this->assertTrue(count(self::$memberRepository->findAll()) === 0);
        // we have this member in db
        [$member] = self::createGoodMemberDataProvider()['GoodMember'];
        $member->setUserName('jean');
        $member->setEmail('jean@mail.com');
        self::$memberRepository->insert($member);

        // we have this member in db
        $this->assertSame(1, count(self::$memberRepository->findAll()));

        // WHEN we use findAllWhere() with column that do not exist : user LIKE '%jean%'
        // EXPECT we retieve 0 member  // this comportement need to change
        $this->assertSame(0, count(self::$memberRepository->findAllWhere('user', 'LIKE', '%jean%')));

        // WHEN we use findAllWhere() with operator
        // not in ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'] : username + '%jean%'
        // EXPECT we retieve 0 member  // this comportement need to change
        $this->assertSame(0, count(self::$memberRepository->findAllWhere('username', '+', '%jean%')));
    }

    #[TestDox('Update a member and ensure getters send the same data')]
    #[DataProvider('createGoodMemberDataProvider')]
    public function testUpdate(MemberEntity $member)
    {
        // GIVEN that table members is empty
        $this->assertTrue(count(self::$memberRepository->findAll()) === 0);
        // and this user exist in db
        $member->setUserName('jeanne');
        $member->setEmail('jeanne@mail.com');
        self::$memberRepository->insert($member);

        // WHEN we update it on db
        $member2 = self::$memberRepository->findByEmail($member->getEmail());
        $date = date_create('1 days ago 12:00');
        $member2->setUserName('John Doe');
        $member2->setEmail('john.doe@mail.com');
        $member2->setPasswordHash('$2y$12$fbnWIaikClpP97zlHyGxwunusDbEv9Mm5ERF613x8t3zydOJjTq5.');
        $member2->setAvatarPath('/upload/avatars/johndoe.png');
        $member2->setCreatedAt($date);
        $member2->setUpdatedAt($date);
        $member2->setNotificationCount(2);
        $member2->setStatus(MemberStatusEnum::NOTVALIDATED);
        self::$memberRepository->update($member2->getId(), $member2);

        // EXPECT getters give the data updated
        $this->assertSame('John Doe', $member2->getUserName());
        $this->assertSame('john.doe@mail.com', $member2->getEmail());
        $this->assertSame('$2y$12$fbnWIaikClpP97zlHyGxwunusDbEv9Mm5ERF613x8t3zydOJjTq5.', $member2->getPasswordHash());
        $this->assertSame('/upload/avatars/johndoe.png', $member2->getAvatarPath());
        $this->assertSame($date, $member2->getCreatedAt());
        $this->assertSame($date, $member2->getUpdatedAt());
        $this->assertSame(2, $member2->getNotificationCount());
        $this->assertSame(MemberStatusEnum::NOTVALIDATED, $member2->getStatus());
    }
}
