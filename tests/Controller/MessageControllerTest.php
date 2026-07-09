<?php

declare(strict_types=1);

namespace Tests\Controller;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Tests\Entity\MessageEntityTest;

class MessageControllerTest extends TestCase
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
            'john@mail.com',
            'P@ssword2024',
            '/upload/avatars/john.png'
        );
        $this->member2 = Settings::getAuthentificationService()->register(
            'Jack',
            'jack@mail.com',
            'P@ssword2024',
            '/upload/avatars/jack.png'
        );
        $this->member3 = Settings::getAuthentificationService()->register(
            'Jean',
            'jean@mail.com',
            'P@ssword2024',
            '/upload/avatars/jean.png'
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

    #[TestDox('showMyBox() logged show My Message Box whith content of all messages on content')]
    public function testShowMyBox()
    {
        Settings::getAuthentificationService()->login(
            $this->member1->getEmail(),
            'P@ssword2024'
        );

        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        Settings::getMessageManager()->sendMessage('Message1', $this->member2);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setFromMember($this->member2);
        $message2->setToMember($this->member1);
        $message2->setContent('Message2');
        Settings::getMessageRepository()->insert($message2);
        Settings::getMessageManager()->sendMessage('Message3', $this->member2);
        Settings::getMessageManager()->sendMessage('Message4', $this->member3);
        Settings::getMessageManager()->sendMessage('Message5', $this->member2);

        $result = Settings::getMessageController()->showMyBox();
        $this->assertMatchesRegularExpression('/Message1/', $result);
        $this->assertMatchesRegularExpression('/Message2/', $result);
        $this->assertMatchesRegularExpression('/Message3/', $result);
        $this->assertMatchesRegularExpression('/Message4/', $result);
        $this->assertMatchesRegularExpression('/Message5/', $result);
    }

    #[TestDox('showMyBox() not logged throw Exception')]
    public function testShowMyProfileNotLogged(): void
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Not Logged');

        Settings::getMessageController()->showMyBox();
    }

    #[TestDox('sendMessage() logged with valid id as string')]
    public function testSendMessageLoggedString()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMessageController()->showMyBox();
        $this->assertDoesNotMatchRegularExpression('/Message1/', $result);

        Settings::getMessageController()->sendMessage('Message1', (string) $this->member2->getId());
        $result = Settings::getMessageController()->showMyBox();
        $this->assertMatchesRegularExpression('/Message1/', $result);
    }

    #[TestDox('sendMessage() logged with valid id as int')]
    public function testSendMessageLoggedInt()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMessageController()->showMyBox();
        $this->assertDoesNotMatchRegularExpression('/Message1/', $result);

        Settings::getMessageController()->sendMessage('Message1', (int) $this->member2->getId());
        $result = Settings::getMessageController()->showMyBox();
        $this->assertMatchesRegularExpression('/Message1/', $result);
    }

    #[TestDox('sendMessage() logged with MemberEntity')]
    public function testSendMessageLoggedMemberEntity()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $result = Settings::getMessageController()->showMyBox();
        $this->assertDoesNotMatchRegularExpression('/Message1/', $result);

        Settings::getMessageController()->sendMessage('Message1', $this->member2);
        $result = Settings::getMessageController()->showMyBox();
        $this->assertMatchesRegularExpression('/Message1/', $result);
    }

    #[TestDox('sendMessage() logged with invalid memberId throw Exception')]
    public function testSendMessageLoggedWithInvalidMemberId()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid memberId');

        Settings::getMessageController()->sendMessage('Message1', 'zz');
    }

    #[TestDox('sendMessage() logged with empty memberId throw Exception')]
    public function testSendMessageLoggedWithEmptyMemberId()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid memberId');

        Settings::getMessageController()->sendMessage('Message1', '');
    }

    #[TestDox('sendMessage() not logged throw Exception')]
    public function testSendMessageNotLogged()
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('You are not logged in');

        Settings::getMessageController()->sendMessage('Message1', $this->member2);
    }

    #[TestDox('getNotificationCount() logged')]
    public function testGetNotificationCountLogged()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->assertSame(
            0,
            Settings::getMessageController()->getNotificationCount()
        );

        $message = MessageEntityTest::instanciateValidMessage();
        $message->setFromMember($this->member2);
        $message->setToMember($this->member1);
        $message->setContent('MessageFromMember2');
        Settings::getMessageRepository()->insert($message);

        $this->assertSame(
            1,
            Settings::getMessageController()->getNotificationCount()
        );
    }

    #[TestDox('getNotificationCount() not logged return 0')]
    public function testGetNotificationCountNotLogged()
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->assertSame(
            0,
            Settings::getMessageController()->getNotificationCount()
        );

        $message = MessageEntityTest::instanciateValidMessage();
        $message->setFromMember($this->member2);
        $message->setToMember($this->member1);
        $message->setContent('MessageFromMember2');
        Settings::getMessageRepository()->insert($message);

        $this->assertSame(
            0,
            Settings::getMessageController()->getNotificationCount()
        );
    }

    #[TestDox('setReadtoAllMessageByUser() logged')]
    public function testSetReadtoAllMessageByUserLogged()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->assertSame(
            0,
            Settings::getMessageController()->getNotificationCount()
        );

        $message = MessageEntityTest::instanciateValidMessage();
        $message->setFromMember($this->member2);
        $message->setToMember($this->member1);
        $message->setContent('MessageFromMember2');
        Settings::getMessageRepository()->insert($message);

        $this->assertSame(
            1,
            Settings::getMessageController()->getNotificationCount()
        );

        Settings::getMessageController()->setReadtoAllMessageByUser((string) $this->member2->getId());

        $this->assertSame(
            0,
            Settings::getMessageController()->getNotificationCount()
        );
    }

    #[TestDox('setReadtoAllMessageByUser() not logged throw Exception')]
    public function testSetReadtoAllMessageByUserNotLogged()
    {
        Settings::getAuthentificationService()->logout();
        $this->assertFalse(Settings::getAuthentificationService()->isLoggedIn());

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('You are not logged');

        Settings::getMessageController()->setReadtoAllMessageByUser((string) $this->member2->getId());
    }

    #[TestDox('setReadtoAllMessageByUser() logged with invalid memberId')]
    public function testSetReadtoAllMessageByUserLoggedWithInvalidMemberId()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->assertSame(
            0,
            Settings::getMessageController()->getNotificationCount()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid memberId');

        Settings::getMessageController()->setReadtoAllMessageByUser('zz');
    }

    #[TestDox('setReadtoAllMessageByUser() logged with empty memberId')]
    public function testSetReadtoAllMessageByUserLoggedWithEmptyMemberId()
    {
        Settings::getAuthentificationService()->login($this->member1->getEmail(), 'P@ssword2024');
        $this->assertSame(
            $this->member1->getEmail(),
            Settings::getAuthentificationService()->getCurrentLoggedMember()->getEmail()
        );

        $this->assertSame(
            0,
            Settings::getMessageController()->getNotificationCount()
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('Invalid memberId');

        Settings::getMessageController()->setReadtoAllMessageByUser('');
    }
}
