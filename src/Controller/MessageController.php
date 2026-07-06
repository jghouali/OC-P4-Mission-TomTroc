<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Core\View\View;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Manager\MemberManager;
use Green\TomTroc\Manager\MessageManager;

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

    public function showMyBox()
    {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $messagesByUser = $this->messageManager->myMessageBox();

            $availableBooksView = new View('Messagerie');
            $data = [
                'messagesByUser' => $messagesByUser,
            ];

            return $availableBooksView->render($data, TEMPLATE_DIR . '/message-box.php');
        } else {
            $errorMessage = 'Not Logged';
            $errorView = new View('Not Logged');
            $data = [
                'errorMessage' => $errorMessage,
            ];
            return $errorView->render($data, TEMPLATE_DIR . '/error.php');
        }
    }

    public function sendMessage(string $content, MemberEntity $member)
    {
        $myid = $this->authentificationService->getCurrentLoggedMember()->getId();
        $messagesByUser = $this->messageManager->sendMessage($content, $member);
        $messagesByUser = $this->messageManager->myMessageBox();

        $availableBooksView = new View('Messagerie');
        $data = [
            'messagesByUser' => $messagesByUser,
        ];

        return $availableBooksView->render($data, TEMPLATE_DIR . '/message-box.php');
    }
}
