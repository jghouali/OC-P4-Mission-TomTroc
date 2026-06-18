<?php

declare(strict_types=1);

namespace Tests\Repository;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Enum\MessageStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\MemberEntityTest;
use Tests\Entity\MessageEntityTest;

class MessageRepositoryTest extends TestCase
{
    // PHPunit fixtures
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

    #[TestDox('insert() and delete()')]
    public function testInsertAndDeleteMessage(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have this message
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($member);
        $message->setToMember($member2);

        // WHEN
        // insert() it in the db
        $result = Settings::getMessageRepository()->insert($message);

        // EXPECT
        // return true
        $this->assertTrue($result);
        // And there is now 1 row in messages table
        $this->assertSame(1, count(Settings::getMessageRepository()->findAll()));

        // WHEN
        // now, we delete it
        $result2 = Settings::getMessageRepository()->delete($message);

        // EXPECT
        // return true
        $this->assertTrue($result2);
        // there is 0 row in message table
        $this->assertSame(0, count(Settings::getMessageRepository()->findAll()));
    }

    #[TestDox('FindAll()')]
    public function testFindAll(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have these 2 messages
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($member);
        $message->setToMember($member2);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($member2);
        $message2->setToMember($member);
        //in the db
        Settings::getMessageRepository()->insert($message);
        Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAll()
        // EXPECT
        // Have 2 messages
        $this->assertSame(2, count(Settings::getMessageRepository()->findAll()));
    }

    #[TestDox('FindAllWhere(\'content\', \'LIKE\', \'%Hello%\')')]
    public function testFindAllWhere(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have these 2 messages
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Salut');
        $message->setFromMember($member);
        $message->setToMember($member2);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($member);
        $message2->setToMember($member2);
        //in the db
        Settings::getMessageRepository()->insert($message);
        Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllWhere()
        // EXPECT
        // return 2
        $this->assertSame(1, count(Settings::getMessageRepository()->findAllWhere('content', 'LIKE', '%Hello%')));
    }

    #[TestDox('FindAllWhere() return [] with invalid informations')]
    public function testFindAllWhereInvalidData(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have these 2 messages
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Salut');
        $message->setFromMember($member);
        $message->setToMember($member2);
        //in the db
        Settings::getMessageRepository()->insert($message);

        // WHEN
        // findAllWhere()
        // EXPECT
        // return 2
        $this->assertSame(0, count(Settings::getMessageRepository()->findAllWhere('contenu', 'LIKE', '%Hello%')));
    }

    #[TestDox('FindById())')]
    public function testFindById(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have this message
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($member);
        $message->setToMember($member2);
        //in the db
        Settings::getMessageRepository()->insert($message);

        // WHEN
        // findById()
        // EXPECT
        // retrieve the message
        $message2 = Settings::getMessageRepository()->findById($message->getId());
        $this->assertSame('Hello', $message2->getContent());
    }

    #[TestDox('FindAllByRecipient()')]
    public function testFindByRecipient(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have these 2 messages
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($member);
        $message->setToMember($member2);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($member);
        $message2->setToMember($member2);
        //in the db
        Settings::getMessageRepository()->insert($message);
        Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllByRecipient()
        // EXPECT
        // return 2
        $this->assertSame(2, count(Settings::getMessageRepository()->findAllByRecipient($member2->getId())));
    }

    #[TestDox('FindAllBySender()')]
    public function testFindBySender(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have these 2 messages
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($member);
        $message->setToMember($member2);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($member);
        $message2->setToMember($member2);
        //in the db
        Settings::getMessageRepository()->insert($message);
        Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllBySender()
        // EXPECT
        // return 2
        $this->assertSame(2, count(Settings::getMessageRepository()->findAllBySender($member->getId())));
    }

    #[TestDox('FindAllByIsRead()')]
    public function testFindByIsRead(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have these 2 messages
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($member);
        $message->setToMember($member2);
        $message->setIsRead(MessageStatusEnum::READ);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($member);
        $message2->setToMember($member2);
        $message2->setIsRead(MessageStatusEnum::READ);
        //in the db
        Settings::getMessageRepository()->insert($message);
        Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllBySender()
        // EXPECT
        // return 2
        $this->assertSame(2, count(Settings::getMessageRepository()->findAllByIsRead(MessageStatusEnum::READ)));
    }

    #[TestDox('update()')]
    public function testUpdate(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have this message
        $member = MemberEntityTest::instanciateValidMember();
        $member->setUserName('john');
        $member->setEmail('john@mail.com');
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jack');
        $member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($member);
        Settings::getMemberRepository()->insert($member2);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($member);
        $message->setToMember($member2);
        //in the db
        Settings::getMessageRepository()->insert($message);

        // WHEN
        // update() it on db
        $message->setContent('Nouveau message de John Doe');
        Settings::getMessageRepository()->update($message->getId(), $message);

        // EXPECT getters give the data updated
        $message2 = Settings::getMessageRepository()->findById($message->getId());
        $this->assertSame('Nouveau message de John Doe', $message2->getContent());
    }
}
