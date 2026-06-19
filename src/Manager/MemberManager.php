<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use Green\TomTroc\Repository\MemberRepository;
use RuntimeException;

class MemberManager
{
    private MemberRepository $memberRepository;
    private AuthentificationService $authentificationService;

    public function __construct(
        MemberRepository $memberRepository,
        AuthentificationService $authentificationService
    ) {
        $this->memberRepository = $memberRepository;
        $this->authentificationService = $authentificationService;
    }

    public function modifyMyProfile(
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ): MemberEntity|false {
        $loggedMember = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedMember === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($loggedMember::class === 'Green\TomTroc\Entity\MemberEntity') {
            $loggedMember->setUserName($username);
            $loggedMember->setEmail($email);
            $loggedMember->setPasswordHash(
                password_hash($password, Settings::get(Settings::APP_SECURITY_HASH_ALGO))
            );
            $loggedMember->setAvatarPath($avatarPath);
            return $this->memberRepository->update($loggedMember->getId(), $loggedMember);
        } else {
            throw new RuntimeException('Unknown Error');
        }
    }

    public function memberExist(string $email): bool
    {
        return (
            $this->memberRepository
                ->findByEmail($email)::class === 'Green\TomTroc\Entity\MemberEntity'
        );
    }

    public function memberExistAndValidated(string $email): bool
    {
        return (
            $this->memberRepository
            ->findByEmail($email)
            ->getStatus() === MemberStatusEnum::VALIDATED
        );
    }

    public function getProfileData(string $username): array
    {
        $member = $this->memberRepository->findByUsername($username);
        $profile = [
            'id' => $member->getId(),
            'username' => $member->getUserName(),
            'email' => $member->getEmail(),
            'avatarPath' => $member->getAvatarPath(),
            'createdAt' => $member->getCreatedAt(),
            'updatedAt' => $member->getUpdatedAt(),
            'notificationCount' => $member->getNotificationCount(),
            'status' => $member->getStatus(),
        ];
        return $profile;
    }
}
