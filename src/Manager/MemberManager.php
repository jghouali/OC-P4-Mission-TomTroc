<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\ProfileEntity;
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
            if ($password !== $loggedMember->getPasswordHash()) {
                $loggedMember->setPasswordHash(
                    $this->authentificationService->generatePasswordHash($password)
                );
            }
            $loggedMember->setAvatarPath($avatarPath);
            return $this->memberRepository->update($loggedMember->getId(), $loggedMember);
        } else {
            throw new RuntimeException('Unknown Error');
        }
    }

    public function getProfileData(int $id): ProfileEntity|false
    {
        $member = $this->memberRepository->findOneById($id);

        if (!$member) {
            return false;
        }

        $profile = new ProfileEntity($member);
        return $profile;
    }
}
