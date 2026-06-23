<?php

declare(strict_types=1);

namespace Tests\Manager;

use Green\TomTroc\Core\Settings\Settings;
use PHPUnit\Framework\TestCase;
use Tests\Entity\MessageEntityTest;

class MessageManagerTest extends TestCase
{
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

    public function testSendMessagesWhenLoggedIn()
    {
        // GIVEN
        Settings::getAuthentificationService()->register(
            'John',
            'john.doe@mail.com',
            'password',
            '/upload/avatars/image.png'
        );
        Settings::getAuthentificationService()->login('john.doe@mail.com', 'password');

        $member2 = Settings::getAuthentificationService()->register(
            'Jack',
            'jack.sparrow@mail.com',
            'password',
            '/upload/avatars/image2.png'
        );
        $content = 'Hello';
        $message = Settings::getMessageManager()->sendMessage($content, $member2);

        $this->assertSame('Hello', $message->getContent());
    }

    public function testSendMessagesWhenNotLoggedInThrowRuntimeException()
    {
        // GIVEN
        $content = 'Hello';
        $member2 = Settings::getAuthentificationService()->register(
            'Jack',
            'jack.sparrow@mail.com',
            'password',
            '/upload/avatars/image2.png'
        );

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('You are not logged in');

        Settings::getMessageManager()->sendMessage($content, $member2);
    }

    public function testMyMessageBox()
    {
        // GIVEN
        $member1 = Settings::getAuthentificationService()->register(
            'John',
            'john.doe@mail.com',
            'password',
            '/upload/avatars/image.png'
        );
        $member2 = Settings::getAuthentificationService()->register(
            'Jack',
            'jack.sparrow@mail.com',
            'password',
            '/upload/avatars/image2.png'
        );
        $member3 = Settings::getAuthentificationService()->register(
            'Jean',
            'jean.valjean@mail.com',
            'password',
            '/upload/avatars/image2.png'
        );

        Settings::getAuthentificationService()->login('john.doe@mail.com', 'password');

        Settings::getMessageManager()->sendMessage('Hello', $member2);
        Settings::getMessageManager()->sendMessage('Hello', $member3);

        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setFromMember($member2);
        $message2->setToMember($member1);
        $message2->setContent('Hello, How are you?');
        Settings::getMessageRepository()->insert($message2);

        $messageArray = Settings::getMessageManager()->myMessageBox();

        $this->assertSame(2, count($messageArray));
    }

    public function testMyMessageBoxWhenNotLoggedInTthrowRuntimeException()
    {
        // GIVEN
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('You are not logged in');

        $messageArray = Settings::getMessageManager()->myMessageBox();
    }
}
