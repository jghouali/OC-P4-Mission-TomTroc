<?php

declare(strict_types=1);

namespace Tests\Manager;

use Green\TomTroc\Core\Settings\Settings;
use PHPUnit\Framework\TestCase;

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

        $content = 'Hello';

        Settings::getAuthentificationService()->login('john.doe@mail.com', 'password');

        $message = Settings::getMessageManager()->sendMessage($content, $member2);

        $this->assertSame('Hello', $message->getContent());
    }

    public function testSendMessagesWhenNotLoggedInThrowRuntimeException()
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

        $content = 'Hello';

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('You are not logged in');

        $message = Settings::getMessageManager()->sendMessage($content, $member2);
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

        Settings::getAuthentificationService()->login('john.doe@mail.com', 'password');

        $message = Settings::getMessageManager()->sendMessage('Hello', $member1);

        $messageArray = Settings::getMessageManager()->myMessageBox();

        $this->assertSame('Hello', $message->getContent());
        $this->assertSame(1, count($messageArray));
    }

    public function testMyMessageBoxWhenNotLoggedInTthrowRuntimeException()
    {
        // GIVEN

        $this->expectException('RuntimeException');
        $this->expectExceptionMessageIs('You are not logged in');

        $messageArray = Settings::getMessageManager()->myMessageBox();
    }
}
