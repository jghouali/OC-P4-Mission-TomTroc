<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Green\TomTroc\Core\Http\Response;
use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Core\View\View;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Manager\MemberManager;
use Green\TomTroc\Manager\MessageManager;
use RuntimeException;

class MessageController
{
    private MessageManager $messageManager;
    private MemberManager $memberManager;
    private AuthentificationService $authentificationService;

    public function __construct(
        MessageManager $messageManager,
        MemberManager $memberManager,
        AuthentificationService $authentificationService
    ) {
        $this->messageManager = $messageManager;
        $this->memberManager = $memberManager;
        $this->authentificationService = $authentificationService;
    }

    public function getNotificationCount(): int
    {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            return $this->messageManager->getNotificationCount();
        } else {
            return 0;
        }
    }

    public function setReadtoAllMessageByUser(string $memberId): Response
    {
        if (!ctype_digit($memberId)) {
            throw new RuntimeException('Invalid memberId', 400);
        }
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $loggedUserId = $loggedUser->getId();
            if ($this->messageManager->setReadtoAllMessageByUser((int) $memberId)) {
                return new Response('OK', 200);
            }
            throw new RuntimeException('Failed to set Read status', 500);
        }
        throw new RuntimeException('You are not logged', 400);
    }

    public function showMyBox(?string $toUser = '')
    {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $messagesByUser = $this->messageManager->myMessageBox();

            $availableBooksView = new View('Messagerie');
            $data = [
                'messagesByUser' => $messagesByUser,
            ];

            if ($toUser !== '') {
                if (!ctype_digit($toUser)) {
                    throw new RuntimeException('Invalid memberId', 400);
                }
                $toUserMember = $this->memberManager->getProfileData((int) $toUser);
                if (!$toUserMember) {
                    throw new RuntimeException('This member doesnt exist', 400);
                }
                $data['toUserMember'] = $toUserMember;
            }

            return $availableBooksView->render($data, TEMPLATE_DIR . '/message-box.php');
        } else {
            throw new RuntimeException('Not Logged', 400);
        }
    }

    public function sendMessage(string $content, MemberEntity|int|string $member)
    {
        if (is_int($member)) {
            $id = $member;
        } elseif (is_string($member)) {
            if (!ctype_digit($member)) {
                throw new RuntimeException('Invalid memberId', 400);
            }
            $id = (int) $member;
        } else {
            $id = $member->getId();
        }
        $this->messageManager->sendMessage($content, $id);

        return new Response('Succes', 303, ['Location:' => '/my-box']);
    }
}
