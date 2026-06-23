<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use Green\TomTroc\Repository\MessageRepository;
use RuntimeException;

class MessageManager
{
    private MemberManager $memberManager;
    private MessageRepository $messageRepository;
    private AuthentificationService $authentificationService;

    public function __construct(
        MessageRepository $messageRepository,
        AuthentificationService $authentificationService,
        MemberManager $memberManager
    ) {
        $this->memberManager = $memberManager;
        $this->messageRepository = $messageRepository;
        $this->authentificationService = $authentificationService;
    }

    public function sendMessage(string $content, MemberEntity $toMember): MessageEntity|false
    {
        $fromMember = $this->authentificationService->getCurrentLoggedMember();
        if ($fromMember === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($fromMember::class === 'Green\TomTroc\Entity\MemberEntity') {
            return $this->messageRepository->insert(
                new MessageEntity(
                    $content,
                    Locales::getLocalDateTime(),
                    Locales::getLocalDateTime(),
                    $fromMember,
                    $toMember,
                    MessageStatusEnum::NOTREAD
                )
            );
        } else {
            throw new RuntimeException('Unknown error');
        }
    }

    public function myMessageBox(): array
    {
        $member = $this->authentificationService->getCurrentLoggedMember();
        if ($member === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($member::class === 'Green\TomTroc\Entity\MemberEntity') {
            $messagesArray = $this->messageRepository->findAllByMember($member);
            $byUserMessage = [];
            foreach ($messagesArray as $row => $value) {
                $memberMessage = $this->memberManager->getProfileData($row);
                $username = $memberMessage->getUsername();
                $byUserMessage[$username] = $value;
            }

            return $byUserMessage;
        } else {
            throw new RuntimeException('Unknown error');
        }
    }
}
