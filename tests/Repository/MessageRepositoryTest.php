<?php

declare(strict_types=1);

namespace Tests\Repository;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\MemberEntityTest;
use Tests\Entity\MessageEntityTest;

class MessageRepositoryTest extends TestCase
{
    private MemberEntity $member1;
    private MemberEntity $member2;

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

        $this->member1 = MemberEntityTest::instanciateValidMember();
        $this->member1->setUserName('john');
        $this->member1->setEmail('john@mail.com');
        Settings::getMemberRepository()->insert($this->member1);

        $this->member2 = MemberEntityTest::instanciateValidMember();
        $this->member2->setUserName('jack');
        $this->member2->setEmail('jack@mail.com');
        Settings::getMemberRepository()->insert($this->member2);
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

    #[TestDox('insert() with valid given data return valid MessageEntity with last insert Id')]
    public function testInsertMessage(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        // And have this message
        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setContent('Hello');
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello2');
        $message2->setFromMember($this->member2);
        $message2->setToMember($this->member1);

        // WHEN
        // insert() it in the db
        $result1 = Settings::getMessageRepository()->insert($message1);
        $result2 = Settings::getMessageRepository()->insert($message2);

        // EXPECT
        // return MemberEntity
        $this->assertSame('Green\TomTroc\Entity\MessageEntity', $result1::class);
        $this->assertSame('Green\TomTroc\Entity\MessageEntity', $result2::class);
        $this->assertSame(
            $message1->getContent(),
            $result1->getContent()
        );
        $this->assertSame(
            $message2->getContent(),
            $result2->getContent()
        );
        $this->assertSame(
            $message1->getFromMember()->getUsername(),
            $result1->getFromMember()->getUsername()
        );
        $this->assertSame(
            $message2->getFromMember()->getUsername(),
            $result2->getFromMember()->getUsername()
        );
        // And there is now 2 row in messages table
        $this->assertSame(2, count(Settings::getMessageRepository()->findAll()));
    }

    #[TestDox('insert() with MessageEntity that already have a valid messageId throw RuntimeException')]
    public function testInsertMessageAlreadyInsert(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($this->member1);
        $message->setToMember($this->member2);

        $message = Settings::getMessageRepository()->insert($message);
        $this->assertNotNull($message->getId());

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('This message already inserted');

        // WHEN
        // insert() it in the db
        Settings::getMessageRepository()->insert($message);
    }

    #[TestDox('insert() with non-existent FromMember memberId throw RuntimeException')]
    public function testInsertMessageNonExistentFromMemberId(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jean');
        $member2->setEmail('jean@mail.com');

        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($member2);
        $message->setToMember($this->member1);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('FromMember Id is null');

        // WHEN
        // insert() it in the db
        Settings::getMessageRepository()->insert($message);
    }

    #[TestDox('insert() with non-existent ToMember memberId throw RuntimeException')]
    public function testInsertMessageNonExistentToMemberId(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $member2 = MemberEntityTest::instanciateValidMember();
        $member2->setUserName('jean');
        $member2->setEmail('jean@mail.com');

        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($this->member1);
        $message->setToMember($member2);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('ToMember Id is null');

        // WHEN
        // insert() it in the db
        Settings::getMessageRepository()->insert($message);
    }

    #[TestDox('update() with valid given data return MessageEntity updated')]
    public function testUpdate(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($this->member1);
        $message->setToMember($this->member2);
        //in the db
        $message = Settings::getMessageRepository()->insert($message);

        // WHEN
        // update() it on db
        $message->setContent('Nouveau message de John Doe');
        $result = Settings::getMessageRepository()->update($message->getId(), $message);

        // EXPECT getters give the data updated
        $this->assertSame('Green\\TomTroc\\Entity\\MessageEntity', $result::class);
        $this->assertSame($message->getContent(), $result->getContent());
    }

    #[TestDox('update() with inexistent messageId throw RuntimeException')]
    public function testUpdateInexistentMessageId(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setFromMember($this->member1);
        $message->setToMember($this->member2);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('messageId doesnt exist');

        // WHEN
        // update() it on db
        Settings::getMessageRepository()->update(145, $message);
    }

    #[TestDox('update() with empty FromMember id throw RuntimeException')]
    public function testUpdateEmptyFromMember(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $member1 = MemberEntityTest::instanciateValidMember();

        $message = MessageEntityTest::instanciateValidMessage();
        $message->setFromMember($member1);
        $message->setToMember($this->member2);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('Null memberId in the given FromMember');

        // WHEN
        // update() it on db
        Settings::getMessageRepository()->update(145, $message);
    }

    #[TestDox('update() with empty ToMember id throw RuntimeException')]
    public function testUpdateEmptyToMember(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $member1 = MemberEntityTest::instanciateValidMember();

        $message = MessageEntityTest::instanciateValidMessage();
        $message->setFromMember($this->member2);
        $message->setToMember($member1);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('Null memberId in the given ToMember');

        // WHEN
        // update() it on db
        Settings::getMessageRepository()->update(145, $message);
    }

    #[TestDox('update() with messageId mismatch with MessageEntity\'s messageId given throw RuntimeException')]
    public function testUpdateMessageIdMismatch(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);

        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);
        $message1->setContent('Hello');
        $message1 = Settings::getMessageRepository()->insert($message1);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setFromMember($this->member2);
        $message2->setToMember($this->member1);
        $message2->setContent('Hi');
        $message2 = Settings::getMessageRepository()->insert($message2);

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('messageId mismatch with messageId whithin the given MemberEntity');

        // WHEN
        // update() it on db
        Settings::getMessageRepository()->update($message1->getId(), $message2);
    }

    #[TestDox('delete() with valid messageId return True')]
    public function testDeleteMessage(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);

        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($this->member1);
        $message->setToMember($this->member2);
        $message = Settings::getMessageRepository()->insert($message);

        $this->assertSame(1, count(Settings::getMessageRepository()->findAll()));

        // WHEN
        // now, we delete it
        $result = Settings::getMessageRepository()->delete($message);

        // EXPECT
        // return true
        $this->assertTrue($result);
        // there is 0 row in message table
        $this->assertSame(0, count(Settings::getMessageRepository()->findAll()));
    }

    #[TestDox('delete() with null messageId throw RuntimeException')]
    public function testDeleteNullMessageId(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);

        $message = MessageEntityTest::instanciateValidMessage();

        $this->assertSame(0, count(Settings::getMessageRepository()->findAll()));

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIsOrContains('messageId is null');

        // WHEN
        // delete it
        Settings::getMessageRepository()->delete($message);
    }

    #[TestDox('FindByOneId()) with an existing messageId return a valid MessageEntity')]
    public function testFindOneById(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($this->member1);
        $message->setToMember($this->member2);
        //in the db
        $message = Settings::getMessageRepository()->insert($message);

        // WHEN
        // findOneById()
        $result = Settings::getMessageRepository()->findOneById($message->getId());

        // EXPECT
        // retrieve the message
        $this->assertSame('Green\\TomTroc\\Entity\\MessageEntity', $result::class);
        $this->assertSame($message->getContent(), $result->getContent());
    }

    #[TestDox('FindByOneId()) with an inexisting messageId return false')]
    public function testFindOneByIdInexistingMessageId(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);

        // WHEN
        // findOneById()
        $result = Settings::getMessageRepository()->findOneById(167);

        // EXPECT
        // return null
        $this->assertNull($result);
    }

    #[TestDox('FindAll() with N Messages in db return an array of N MessageEntity')]
    public function testFindAll(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setContent('Hello');
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);
        $message1 = Settings::getMessageRepository()->insert($message1);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($this->member2);
        $message2->setToMember($this->member1);
        $message2 = Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAll()
        // EXPECT
        // Have 2 messages
        $this->assertSame(2, count(Settings::getMessageRepository()->findAll()));
    }

    #[TestDox('FindAll() with empty db return an array of 0 MessageEntity')]
    public function testFindAllEmptyDb(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);

        // WHEN
        // findAll()
        $result = Settings::getMessageRepository()->findAll();
        // EXPECT
        // Have 0 messages
        $this->assertSame(0, count($result));
    }

    #[TestDox('FindAllByRecipient() with N Messages to this Recipient return an array of N MessageEntity')]
    public function testFindAllByRecipient(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setContent('Hello');
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);
        $message1 = Settings::getMessageRepository()->insert($message1);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($this->member1);
        $message2->setToMember($this->member2);
        $message2 = Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllByRecipient()
        $result = Settings::getMessageRepository()->findAllByRecipient($this->member2->getId());
        // EXPECT
        // return 2
        $this->assertSame(2, count($result));
    }

    #[TestDox('FindAllByRecipient() with 0 Messages to this Recipient return an array of 0 MessageEntity')]
    public function testFindAllByRecipientZero(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);

        // WHEN
        // findAllByRecipient()
        $result = Settings::getMessageRepository()->findAllByRecipient($this->member2->getId());
        // EXPECT
        // return 0
        $this->assertSame(0, count($result));
    }

    #[TestDox('FindAllBySender() with N Messages to this Recipient return an array of N MessageEntity')]
    public function testFindAllBySender(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message = MessageEntityTest::instanciateValidMessage();
        $message->setContent('Hello');
        $message->setFromMember($this->member1);
        $message->setToMember($this->member2);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($this->member1);
        $message2->setToMember($this->member2);
        //in the db
        Settings::getMessageRepository()->insert($message);
        Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllBySender()
        $result = Settings::getMessageRepository()->findAllBySender($this->member1->getId());

        // EXPECT
        // return 2
        $this->assertSame(2, count($result));
    }

    #[TestDox('FindAllBySender() with 0 Messages to this Recipient return an array of 0 MessageEntity')]
    public function testFindAllBySenderZero(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);

        // WHEN
        // findAllBySender()
        $result = Settings::getMessageRepository()->findAllBySender($this->member1->getId());

        // EXPECT
        // return 0
        $this->assertSame(0, count($result));
    }

    #[TestDox('FindAllByIsRead() with N Messages to this Recipient return an array of N MessageEntity')]
    public function testFindAllByIsRead(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setContent('Hello');
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);
        $message1->setIsRead(MessageStatusEnum::READ);
        $message1 = Settings::getMessageRepository()->insert($message1);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($this->member1);
        $message2->setToMember($this->member2);
        $message2->setIsRead(MessageStatusEnum::READ);
        $message2 = Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllBySender()
        $result = Settings::getMessageRepository()->findAllByIsRead(MessageStatusEnum::READ);
        // EXPECT
        // return 2
        $this->assertSame(2, count($result));
    }

    #[TestDox('FindAllByIsRead() with 0 Messages to this Recipient return an array of 0 MessageEntity')]
    public function testFindAllByIsReadZero(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setContent('Hello');
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);
        $message1->setIsRead(MessageStatusEnum::NOTREAD);
        $message1 = Settings::getMessageRepository()->insert($message1);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($this->member1);
        $message2->setToMember($this->member2);
        $message2->setIsRead(MessageStatusEnum::NOTREAD);
        $message2 = Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllBySender()
        $result = Settings::getMessageRepository()->findAllByIsRead(MessageStatusEnum::READ);
        // EXPECT
        // return 0
        $this->assertSame(0, count($result));
    }

    #[TestDox('FindAllByMemberNotRead() with N Messages to this Recipient return an array of N MessageEntity')]
    public function testFindAllByMemberNotRead(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setContent('Hello');
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);
        $message1->setIsRead(MessageStatusEnum::NOTREAD);
        $message1 = Settings::getMessageRepository()->insert($message1);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($this->member1);
        $message2->setToMember($this->member2);
        $message2->setIsRead(MessageStatusEnum::NOTREAD);
        $message2 = Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllBySender()
        $result = Settings::getMessageRepository()->findAllByMemberNotRead($this->member2);
        // EXPECT
        // return 2
        $this->assertSame(2, count($result));
    }

    #[TestDox('FindAllByMemberNotRead() with 0 Messages to this Recipient return an array of 0 MessageEntity')]
    public function testFindAllByMemberNotReadZero(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setContent('Hello');
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);
        $message1->setIsRead(MessageStatusEnum::READ);
        $message1 = Settings::getMessageRepository()->insert($message1);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($this->member1);
        $message2->setToMember($this->member2);
        $message2->setIsRead(MessageStatusEnum::READ);
        $message2 = Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllBySender()
        $result = Settings::getMessageRepository()->findAllByMemberNotRead($this->member2);
        // EXPECT
        // return 0
        $this->assertSame(0, count($result));
    }

    #[TestDox('FindAllByMemberSorted() with N Messages to this Recipient return an array' .
        ' of N MessageEntity in an array of N Senders')]
    public function testFindAllByMemberSorted(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);
        $message1 = MessageEntityTest::instanciateValidMessage();
        $message1->setContent('Hello');
        $message1->setFromMember($this->member1);
        $message1->setToMember($this->member2);
        $message1->setIsRead(MessageStatusEnum::NOTREAD);
        $message1 = Settings::getMessageRepository()->insert($message1);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setContent('Hello, how are you ?');
        $message2->setFromMember($this->member1);
        $message2->setToMember($this->member2);
        $message2->setIsRead(MessageStatusEnum::NOTREAD);
        $message2 = Settings::getMessageRepository()->insert($message2);

        // WHEN
        // findAllBySender()
        $result = Settings::getMessageRepository()->findAllByMemberSorted($this->member2);
        // EXPECT
        // return 1
        $this->assertSame(1, count($result));
        $this->assertSame(2, count($result[$this->member1->getId()]));
    }

    #[TestDox('FindAllByMemberSorted() with 0 Messages to this Recipient return an array ' .
        'of 0 MessageEntity in an array of 0 Member')]
    public function testFindAllByMemberSortedZero(): void
    {
        // GIVEN
        // messages table is empty
        $this->assertTrue(count(Settings::getMessageRepository()->findAll()) === 0);

        // WHEN
        // findAllBySender()
        $result = Settings::getMessageRepository()->findAllByMemberSorted($this->member2);
        // EXPECT
        // return 0
        $this->assertSame(0, count($result));
    }
}
