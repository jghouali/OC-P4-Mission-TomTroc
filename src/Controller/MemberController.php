<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Green\TomTroc\Core\Http\Response;
use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Core\Service\ValidatorService;
use Green\TomTroc\Core\View\View;
use Green\TomTroc\Enum\ValidatorEnum;
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

    public function showRegister(): string|Response
    {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            return new Response('Already registered in', 302, ['Location:' => '/my-profile']);
        };
        $registerView = new View('Inscription');
        $data = [];
        return $registerView->render($data, TEMPLATE_DIR . '/register.php');
    }

    public function login(string $email, string $password)
    {
        $logged = $this->authentificationService->login(
            $email,
            $password
        );
        if ($logged) {
            return $this->showMyProfile();
        }
        return $this->showLogin(true);
    }

    public function logout(): Response
    {
        $notLogged = $this->authentificationService->logout();
        return new Response('Succes', 303, ['Location:' => '/']);
    }

    public function register(string $username, string $email, string $password): string
    {
        if ($this->memberManager->emailAlreadyRegistered($email)) {
            throw new RuntimeException("$email already registered", 400);
        };
        if ($this->memberManager->usernameAlreadyRegistered($username)) {
            throw new RuntimeException("$username already registered", 400);
        };

        $member = $this->authentificationService->register(
            $username,
            $email,
            $password,
            '/upload/avatars/default-avatar.png'
        );
        if ($member::class === 'Green\TomTroc\Entity\MemberEntity') {
            return $this->login($email, $password);
        }
        throw new RuntimeException('Error occured while regester this member');
    }

    public function showLogin(?bool $showLoginError = null): string|Response
    {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            return new Response('Already Logged in', 302, ['Location:' => '/my-profile']);
        } else {
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
            throw new RuntimeException('Not Logged', 400);
        }
    }

    public function modifyMyProfile(
        ?string $email = '',
        ?string $password = '',
        ?string $username = '',
        mixed $avatarPath = ''
    ): Response {
        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null) {
            if ($this->memberManager->emailAlreadyRegistered($email)) {
                throw new RuntimeException("$email already registered", 400);
            };
            if ($this->memberManager->usernameAlreadyRegistered($username)) {
                throw new RuntimeException("$username already registered", 400);
            };

            if ($email === '') {
                $email = $loggedUser->getEmail();
            }
            if ($password === '') {
                $passwordHash = $loggedUser->getPasswordHash();
            } else {
                $passwordHash = $password;
            }
            if ($username === '') {
                $username = $loggedUser->getUserName();
            }
            if ($avatarPath['error'] === 4) {
                $avatarPath = $loggedUser->getAvatarPath();
            }

            $error = [
                'username' => false,
                'email' => false,
                'password' => false,
                'avatarPath' => false,
            ];

            if (
                !ValidatorService::validateField(
                    'username',
                    $username,
                    ValidatorEnum::textContent50,
                )
            ) {
                $error['username'] = true;
            }

            if (
                !ValidatorService::validateField(
                    'email',
                    $email,
                    ValidatorEnum::email,
                )
            ) {
                $error['email'] = true;
            }

            if ($password !== '') {
                if (
                    !ValidatorService::validateField(
                        'password',
                        $password,
                        ValidatorEnum::textContent50
                    )
                ) {
                    $error['password'] = true;
                }
            }

            if (
                is_string($avatarPath)
            ) {
                if (
                    !ValidatorService::validateField(
                        'avatarPath',
                        $avatarPath,
                        ValidatorEnum::imagePath
                    )
                ) {
                    $error['avatarPath'] = true;
                }
            } elseif (is_array($avatarPath)) {
                $avatarPath = ValidatorService::validateField(
                    'avatarPath',
                    $avatarPath,
                    ValidatorEnum::uploadFile
                );
                if (!is_string($avatarPath)) {
                    $error['avatarPath'] = true;
                }
            } else {
                throw new RuntimeException('Unknow avatar type');
            }

            if (in_array(true, $error, true)) {
                $errorArray = [];
                foreach ($error as $field => $isError) {
                    if ($isError) {
                        $errorArray[$field] = $isError;
                    }
                }

                $errorMessage = 'There is error on field : ' . implode('\', \'');
                throw new RuntimeException($errorMessage, 400);
            }

            $myInformations = $this->memberManager->modifyMyProfile(
                username: $username,
                email: $email,
                password: $passwordHash,
                avatarPath: $avatarPath
            );
            if ($myInformations::class === 'Green\TomTroc\Entity\MemberEntity') {
                return new Response('Update successfully', 303, ['Location:' => '/my-profile']);
            } else {
                throw new RuntimeException('an error occured while updating member profile', 500);
            }
        }
        throw new RuntimeException('Not Logged', 400);
    }

    public function showProfile(int|string $memberId): string|Response
    {
        if (is_string($memberId) && !ctype_digit($memberId)) {
            throw new RuntimeException('Invalid memberId', 400);
        }

        $member = $this->memberManager->getProfileData((int) $memberId);

        $loggedUser = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedUser !== null && $loggedUser->getId() === (int) $memberId) {
            return new Response('Redirect to /my-profile', 302, ['Location:' => '/my-profile']);
        }

        if (!$member) {
            throw new RuntimeException("memberId $memberId does not exist", 400);
        }

        $data = [
            'profile' => $member,
        ];

        $registerView = new View($member->getUsername());

        return $registerView->render($data, TEMPLATE_DIR . '/profile.php');
    }
}
