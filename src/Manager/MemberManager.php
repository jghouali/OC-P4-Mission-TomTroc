<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use Green\TomTroc\Repository\MemberRepository;
use RuntimeException;

class MemberManager
{
    private MemberRepository $memberRepository;
    private string $passwordHashAlgorithm;

    public function __construct()
    {
        $this->memberRepository = Settings::getMemberRepository();
        $this->passwordHashAlgorithm = Settings::get(Settings::APP_SECURITY_HASH_ALGO, PASSWORD_DEFAULT);
    }
    public function register(string $username, string $email, string $password, string $avatar_path): bool
    {
        $passwordHash = password_hash(
            $password,
            $this->passwordHashAlgorithm
        );
        return $this->memberRepository->insert(
            new MemberEntity(
                $username,
                $email,
                $passwordHash,
                $avatar_path,
                Locales::getLocalDateTime(),
                Locales::getLocalDateTime(),
                0,
                MemberStatusEnum::NOTVALIDATED
            )
        );
    }

    public function login(string $email, string $password): bool
    {
        $member = $this->memberRepository->findByEmail($email);
        $hash = $member->getPasswordHash();

        if (is_string($hash)) {
            if (password_verify($password, $hash)) {
                $_SESSION['id'] = $member->getId();
                $_SESSION['avatarPath'] = $member->getAvatarPath();
                $_SESSION['username'] = $member->getUserName();
                return true;
            }
            return false;
        }
        throw new RuntimeException('This email is not registered');
    }

    public function isLoggedIn(MemberEntity $member): bool
    {
        if (isset($_SESSION)) {
            if (isset($_SESSION['id'])) {
                if ($_SESSION['id'] == $member->getId()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function modifyProfile(
        MemberEntity $member,
        string $username,
        string $email,
        string $password,
        string $avatarPath
    ): bool {
        if ($this->isLoggedIn($member)) {
            $member->setUserName($username);
            $member->setEmail($email);
            $member->setPasswordHash(password_hash($password, Settings::get(Settings::APP_SECURITY_HASH_ALGO)));
            $member->setAvatarPath($avatarPath);
            $this->memberRepository->update($member->getId(), $member);
            return true;
        }
        return false;
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
