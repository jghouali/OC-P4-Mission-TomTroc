<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use Green\TomTroc\Repository\MemberRepository;
use Green\TomTroc\Repository\MessageRepository;
use RuntimeException;

class MessageManager
{
    private MemberManager $memberManager;
    private MessageRepository $messageRepository;
    private MemberRepository $memberRepository;
    private AuthentificationService $authentificationService;

    public function __construct(
        MessageRepository $messageRepository,
        MemberRepository $memberRepository,
        AuthentificationService $authentificationService,
        MemberManager $memberManager
    ) {
        $this->memberManager = $memberManager;
        $this->memberRepository = $memberRepository;
        $this->messageRepository = $messageRepository;
        $this->authentificationService = $authentificationService;
    }

    public function sendMessage(string $content, MemberEntity|int $toMember): MessageEntity|false
    {
        $fromMember = $this->authentificationService->getCurrentLoggedMember();
        if ($fromMember === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($fromMember::class === 'Green\TomTroc\Entity\MemberEntity') {
            if (is_int($toMember)) {
                $member = $this->memberRepository->findOneById($toMember);
            } else {
                $member = $toMember;
            }

            return $this->messageRepository->insert(
                new MessageEntity(
                    $content,
                    Locales::getLocalDateTime(),
                    Locales::getLocalDateTime(),
                    $fromMember,
                    $member,
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
            $messagesArray = $this->messageRepository->findAllByMemberSorted($member);
            $byUserMessage = [];
            foreach ($messagesArray as $row => $value) {
                $memberMessage = $this->memberManager->getProfileData($row);
                $username = $memberMessage->getUsername();
                $byUserMessage[$username]['messages'] = $value;
                $byUserMessage[$username]['profileObject'] = $memberMessage;
            }

            return $byUserMessage;
        } else {
            throw new RuntimeException('Unknown error');
        }
    }

    public function getNotificationCount(): int
    {
        $member = $this->authentificationService->getCurrentLoggedMember();
        if ($member === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($member::class === 'Green\TomTroc\Entity\MemberEntity') {
            $messagesArray = $this->messageRepository->findAllByMemberNotRead($member);
            return count($messagesArray);
        }
        return 0;
    }

    public function setReadtoAllMessageByUser(MemberEntity|int $fromMember): bool
    {

        $member = $this->authentificationService->getCurrentLoggedMember();
        if ($member === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($member::class === 'Green\TomTroc\Entity\MemberEntity') {
            if (is_int($fromMember)) {
                $fromMemberEntity = $this->memberRepository->findOneById($fromMember);
            } else {
                $fromMemberEntity = $fromMember;
            }

            return $this->messageRepository->setReadtoAllMessageByUser($member, $fromMemberEntity);
        }
        return false;
    }
}
