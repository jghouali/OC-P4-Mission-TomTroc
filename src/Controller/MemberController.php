<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Green\TomTroc\Core\Http\Response;
use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Core\View\View;
use Green\TomTroc\Manager\BookManager;
use Green\TomTroc\Manager\MemberManager;
use RuntimeException;

class MemberController
{
    private MemberManager $memberManager;
    private BookManager $bookManager;
    private AuthentificationService $authentificationService;

    public function __construct(
        BookManager $bookManager,
        MemberManager $memberManager,
        AuthentificationService $authentificationService
    ) {
        $this->bookManager = $bookManager;
        $this->memberManager = $memberManager;
        $this->authentificationService = $authentificationService;
    }

    public function showRegister(): string
    {
        $registerView = new View('Inscription');
        $data = [];
        return $registerView->render($data, TEMPLATE_DIR . '/register.php');
    }

    public function login(string $email, string $password): Response
    {
        $logged = $this->authentificationService->login(
            $email,
            $password
        );
        if ($logged) {
            return new Response('Succes', 303, ['Location:' => '/']);
        }
        return new Response($this->showLogin(true), 200);
    }

    public function logout(): Response
    {
        $notLogged = $this->authentificationService->logout();
        return new Response('Succes', 303, ['Location:' => '/']);
    }

    public function register(string $username, string $email, string $password): string
    {
        $member = $this->authentificationService->register(
            $username,
            $email,
            $password,
            '/upload/avatars/default-avatar.png'
        );
        if ($member::class === 'Green\TomTroc\Entity\MemberEntity') {
            $registerView = new View('Inscription');
            $data = [];
            return $registerView->render($data, TEMPLATE_DIR . '/register.php');
        }
        throw new RuntimeException('Error occured while regester this member');
    }

    public function showLogin(?bool $showLoginError = null): string
    {
        $registerView = new View('Connexion');
        if ($showLoginError === null || $showLoginError === false) {
            $data = [];
        } else {
            $data = [
                'loginError' => 'Login error : check your credential',
            ];
        }
        return $registerView->render($data, TEMPLATE_DIR . '/login.php');
    }

    public function showMyProfile(): string
    {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            $myid = $this->authentificationService->getCurrentLoggedMember()->getId();
            $myInformations = $this->memberManager->getProfileData($myid);
            $registerView = new View('Mon Compte');
            $data = [
                'myInformations' => $myInformations,
            ];
            return $registerView->render($data, TEMPLATE_DIR . '/my-profile.php');
        } else {
            $errorMessage = 'Not Logged';
            $errorView = new View('Not Logged');
            $data = [
                'errorMessage' => $errorMessage,
            ];
            return $errorView->render($data, TEMPLATE_DIR . '/error.php');
        }
    }

    public function showProfile(int $id): string
    {
        $member = $this->memberManager->getProfileData($id);
        $data = [
            'username' => $member->getUsername(),
            'avatarPath' => $member->getAvatarPath(),
            'memberSince' => $member->getMemberSince(),
            'books' => $member->getBooks(),
            'bookCount' => $member->getBookCount(),
        ];

        $registerView = new View($member->getUsername());

        return $registerView->render($data, TEMPLATE_DIR . '/profile.php');
    }
}
