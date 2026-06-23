<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\ProfileEntity;
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
                ->findOneByEmail($email)::class === 'Green\TomTroc\Entity\MemberEntity'
        );
    }

    public function memberExistAndValidated(string $email): bool
    {
        return (
            $this->memberRepository
            ->findOneByEmail($email)
            ->getStatus() === MemberStatusEnum::VALIDATED
        );
    }

    public function getProfileData(int $id): ProfileEntity
    {
        $member = $this->memberRepository->findOneById($id);
        $profile = new ProfileEntity($member);
        return $profile;
    }

    public function getMyProfileData(): array
    {
        $loggedMember = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedMember === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($loggedMember::class === 'Green\TomTroc\Entity\MemberEntity') {
            $profile = [
                'id' => $loggedMember->getId(),
                'username' => $loggedMember->getUserName(),
                'email' => $loggedMember->getEmail(),
                'avatarPath' => $loggedMember->getAvatarPath(),
                'createdAt' => $loggedMember->getCreatedAt(),
                'updatedAt' => $loggedMember->getUpdatedAt(),
                'notificationCount' => $loggedMember->getNotificationCount(),
                'status' => $loggedMember->getStatus(),
            ];
        } else {
            throw new RuntimeException('Unknown Error');
        }
        return $profile;
    }
}
