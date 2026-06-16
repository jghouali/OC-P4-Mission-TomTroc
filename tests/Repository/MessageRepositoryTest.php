<?php

declare(strict_types=1);

namespace Tests\Repository;

use DateTime;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use Green\TomTroc\Enum\MessageStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\MemberEntityTest;
use Tests\Entity\MessageEntityTest;

class MessageRepositoryTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
        Settings::getMessageRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    public function setUp(): void
    {
        Settings::getMessageRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    public function tearDown(): void
    {
        Settings::getMessageRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    public static function tearDownAfterClass(): void
    {
        Settings::getMessageRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
    }

    public function createMessage(
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fkFromMemberId,
        MemberEntity $fkToMemberId,
        MessageStatusEnum $isRead
    ) {
        return new MessageEntity(
            $content,
            $sentAt,
            $modifiedAt,
            $fkFromMemberId,
            $fkToMemberId,
            $isRead
        );
    }

    public static function createGoodMessageDataProvider(): array
    {
        $memberArray = MemberEntityTest::validDataProvider()['GoodMember'];
        [$member] = $memberArray;

        return [
            'GoodMessage' => [
                new MessageEntity(
                    MessageEntityTest::$goodContent,
                    date_create('yesterday at 12:00'),
                    date_create('yesterday at 12:00'),
                    $member,
                    $member,
                    MessageStatusEnum::NOTREAD
                ),
            ],
        ];
    }

    #[TestDox('Save a Message with valid data and delete it')]
    public function testCanSaveAndDeleteAMessage()
    {
        // GIVEN that table messages is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // and we have this message
        $date = date_create('Today 12:00');
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
        $member2 = new MemberEntity(
            'Matthieu',
            'matthieu@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            $date,
            $date,
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);

        $message = new MessageEntity(
            'Hello from me',
            date_create('Today 12:00'),
            date_create('Today 12:00'),
            $member,
            $member2,
            MessageStatusEnum::NOTREAD
        );
        //in the db
        Settings::getMessageRepository()->insert($message);

        // EXPECT it is stored in db and there is now one row in table messages
        $this->assertSame(1, count(Settings::getMessageRepository()->findAll()));

        $this->assertSame(1, count(Settings::getMessageRepository()->findAllByRecipient($member2->getId())));

        // WHEN now, we delete it
        Settings::getMessageRepository()->delete($message);

        // EXPECT there is 0 row in message table
        $this->assertSame(0, count(Settings::getMessageRepository()->findAll()));

        Settings::getMemberRepository()->delete($member);
    }

    #[TestDox('FindAllByRecipient and ensure we get the exact count of message')]
    public function testFindByRecipient()
    {
        // GIVEN that table messages is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // and we have this message
        $date = date_create('Today 12:00');
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
        $member2 = new MemberEntity(
            'Matthieu',
            'matthieu@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            $date,
            $date,
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);

        $message = new MessageEntity(
            'Hello from me',
            date_create('Today 12:00'),
            date_create('Today 12:00'),
            $member,
            $member2,
            MessageStatusEnum::NOTREAD
        );
        //in the db
        Settings::getMessageRepository()->insert($message);

        // WHEN we search for it with findAllByRecipient
        // EXPECT we have the exact count of message
        $this->assertSame(
            1,
            count(Settings::getMessageRepository()->findAllByRecipient($message->getToMember()->getId()))
        );
    }

    #[TestDox('FindAllBySender and ensure we get the exact count of message')]
    public function testFindBySender()
    {
        // GIVEN that table messages is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // and we have this message
        $date = date_create('Today 12:00');
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
        $member2 = new MemberEntity(
            'Matthieu',
            'matthieu@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            $date,
            $date,
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);

        $message = new MessageEntity(
            'Hello from me',
            date_create('Today 12:00'),
            date_create('Today 12:00'),
            $member,
            $member2,
            MessageStatusEnum::NOTREAD
        );
        //in the db
        Settings::getMessageRepository()->insert($message);

        // WHEN we search for it with findAllByRecipient
        // EXPECT we have the exact count of message
        $this->assertSame(
            1,
            count(Settings::getMessageRepository()->findAllBySender($message->getFromMember()->getId()))
        );
    }

    #[TestDox('FindById a message and ensure getters send the same data')]
    public function testFindById()
    {
        // GIVEN that table messages is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // and we have this message
        $date = date_create('Today 12:00');
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
        $member2 = new MemberEntity(
            'Matthieu',
            'matthieu@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            $date,
            $date,
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);

        $message = new MessageEntity(
            'Hello from me',
            date_create('Today 12:00'),
            date_create('Today 12:00'),
            $member,
            $member2,
            MessageStatusEnum::NOTREAD
        );
        //in the db
        Settings::getMessageRepository()->insert($message);

        // WHEN we FindById it on db
        // EXPECT getters give the data updated
        $message2 = Settings::getMessageRepository()->findById($message->getId());
        $this->assertSame('Hello from me', $message2->getContent());
    }

    #[TestDox('Update a message and ensure getters send the same data')]
    public function testUpdate()
    {
        // GIVEN that table messages is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // and we have this message
        $date = date_create('Today 12:00');
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
        $member2 = new MemberEntity(
            'Matthieu',
            'matthieu@mail.com',
            '$2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu',
            '/upload/avatars/cnsk4qcds54xvx5.png',
            $date,
            $date,
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);

        $message = new MessageEntity(
            'Hello from me',
            date_create('Today 12:00'),
            date_create('Today 12:00'),
            $member,
            $member2,
            MessageStatusEnum::NOTREAD
        );
        //in the db
        Settings::getMessageRepository()->insert($message);

        // WHEN we update it on db
        $message->setContent('Nouveau message de John Doe');

        Settings::getMessageRepository()->update($message->getId(), $message);

        // EXPECT getters give the data updated
        $message2 = Settings::getMessageRepository()->findById($message->getId());
        $this->assertSame('Nouveau message de John Doe', $message2->getContent());
    }
}
