<?php

declare(strict_types=1);

namespace Tests\Controller;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
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
            'password',
            '/upload/avatars/john.png'
        );
        $this->member2 = Settings::getAuthentificationService()->register(
            'Jack',
            'jack@mail.com',
            'password',
            '/upload/avatars/jack.png'
        );
        $this->member3 = Settings::getAuthentificationService()->register(
            'Jean',
            'jean@mail.com',
            'password',
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

    public function testShowMyBox()
    {
        Settings::getAuthentificationService()->login('john@mail.com', 'password');

        Settings::getMessageManager()->sendMessage('Hello', $this->member2);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setFromMember($this->member2);
        $message2->setToMember($this->member1);
        $message2->setContent('Hello too');
        Settings::getMessageRepository()->insert($message2);
        Settings::getMessageManager()->sendMessage('How are you ?', $this->member2);
        Settings::getMessageManager()->sendMessage('Hey ?', $this->member3);
        Settings::getMessageManager()->sendMessage('How are you ?', $this->member2);

        $result = Settings::getMessageController()->showMyBox();
        $this->assertMatchesRegularExpression('/How are you ?/', $result);
    }

    public function testSendMessage()
    {
        Settings::getAuthentificationService()->login('john@mail.com', 'password');

        Settings::getMessageManager()->sendMessage('Hello', $this->member2);
        $message2 = MessageEntityTest::instanciateValidMessage();
        $message2->setFromMember($this->member2);
        $message2->setToMember($this->member1);
        $message2->setContent('Hello too');
        Settings::getMessageRepository()->insert($message2);
        Settings::getMessageManager()->sendMessage('How are you ?', $this->member2);
        Settings::getMessageManager()->sendMessage('Hey ?', $this->member3);
        Settings::getMessageManager()->sendMessage('How are you ?', $this->member2);

        $result = Settings::getMessageController()->showMyBox();
        $this->assertMatchesRegularExpression('/How are you ?/', $result);

        $this->assertMatchesRegularExpression(
            '/I am fine/',
            Settings::getMessageController()->sendMessage('I am fine', $this->member2)
        );
    }
}
