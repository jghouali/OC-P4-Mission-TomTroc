<?php

declare(strict_types=1);

namespace Tests\Manager;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\MessageEntityTest;

class MessageManagerTest extends TestCase
{
    private MemberEntity $member1;
    private MemberEntity $member2;
    private MemberEntity $member3;

    public static function setUpBeforeClass(): void
    {
        Settings::addSettingsFile(ROOT_DIR . '/config/custom.php');
        Settings::initialize();
        Settings::getMessageRepository()->deleteAll();
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['avatarPath']);
    }

    public function setUp(): void
    {
        Settings::getMessageRepository()->deleteAll();
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['avatarPath']);

        $this->member1 = Settings::getAuthentificationService()->register(
            'John',
            'john.doe@mail.com',
            'P@ssword2026',
            '/upload/avatars/image.png'
        );
        $this->member2 = Settings::getAuthentificationService()->register(
            'Jack',
            'jack.sparrow@mail.com',
            'P@ssword2026',
            '/upload/avatars/image2.png'
        );
    }

    public function tearDown(): void
    {
        Settings::getMessageRepository()->deleteAll();
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['avatarPath']);
    }

    public static function tearDownAfterClass(): void
    {
        Settings::getMessageRepository()->deleteAll();
        Settings::getBookRepository()->deleteAll();
        Settings::getMemberRepository()->deleteAll();
        unset($_SESSION['id']);
        unset($_SESSION['username']);
        unset($_SESSION['avatarPath']);
    }

    #[TestDox('sendMessage() with valid Data when Logged return a valid MessageEntity with a valid Id')]
    public function testSendMessagesWhenLoggedIn()
    {
        // GIVEN
        // empty db
        $this->assertSame(0, count(Settings::getMessageRepository()->findAll()));
        // and logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2026');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        // WHEN sendMessage()
        $result = Settings::getMessageManager()->sendMessage('Hello', $this->member2);

        // EXPECT
        // return MessageEntity
        $this->assertSame('Green\TomTroc\Entity\MessageEntity', $result::class);
        $this->assertSame('Hello', $result->getContent());
        $this->assertTrue((is_int($result->getId()) && $result->getId() > 0));
        // 1 message in db
        $this->assertSame(1, count(Settings::getMessageRepository()->findAll()));
    }

    #[TestDox('sendMessage() when not Logged throw RuntimeException')]
    public function testSendMessagesWhenNotLoggedInThrowRuntimeException()
    {
        // GIVEN
        // empty db
        $this->assertSame(0, count(Settings::getMessageRepository()->findAll()));
        // and not logged in
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('You are not logged in');

        // WHEN sendMessage()
        Settings::getMessageManager()->sendMessage('Hello', $this->member2);
    }

    #[TestDox('myMessageBox() with valid Data when Logged return an array of 2 messages' .
        ' whitin an array of 1 member and the profileObject')]
    public function testMyMessageBox()
    {
        // GIVEN
        // empty db
        $this->assertSame(0, count(Settings::getMessageRepository()->findAll()));
        // and logged in
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2026');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        Settings::getMessageManager()->sendMessage('Hello', $this->member2);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setFromMember($this->member2);
        $message2->setToMember($this->member1);
        $message2->setContent('Hello, How are you?');
        $message2 = Settings::getMessageRepository()->insert($message2);

        // WHEN myMessageBox()
        $result = Settings::getMessageManager()->myMessageBox();

        // EXPECT
        // return an array of 2 messages whitin an array of 1 member
        $this->assertSame(1, count($result));
        $this->assertSame(
            2,
            count($result[$this->member2->getUserName()]['messages'])
        );
        $this->assertSame(
            'Green\TomTroc\Entity\ProfileEntity',
            $result[$this->member2->getUserName()]['profileObject']::class
        );
    }

    #[TestDox('myMessageBox() not Logged throw RuntimeException')]
    public function testMyMessageBoxWhenNotLoggedInTthrowRuntimeException()
    {
        // GIVEN
        // empty db
        $this->assertSame(0, count(Settings::getMessageRepository()->findAll()));
        // and not logged in
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        // EXPECT
        // throw RuntimeException
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('You are not logged in');

        // WHEN myMessageBox()
        Settings::getMessageManager()->myMessageBox();
    }
}
