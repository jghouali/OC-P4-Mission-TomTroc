<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Service;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use Green\TomTroc\Repository\MemberRepository;
use RuntimeException;

class AuthentificationService
{
    private MemberRepository $memberRepository;
    private string $passwordHashAlgorithm;

    public function __construct(?MemberRepository $memberRepository = null)
    {
        $this->memberRepository = $memberRepository ?? Settings::getMemberRepository();
        $this->passwordHashAlgorithm = Settings::get(Settings::APP_SECURITY_HASH_ALGO, PASSWORD_DEFAULT);
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['id']);
    }

    public function getCurrentLoggedMember(): ?MemberEntity
    {
        if (isset($_SESSION['id'])) {
            $result = Settings::getMemberRepository()->findOneById($_SESSION['id']);
        } else {
            $result = null;
        }
        return $result;
    }

    public function generatePasswordHash(string $password): string
    {
        return password_hash(
            $password,
            $this->passwordHashAlgorithm
        );
    }

    public function register(string $username, string $email, string $password, string $avatar_path): MemberEntity|false
    {
        $passwordHash = $this->generatePasswordHash($password);

        $member = new MemberEntity(
            $username,
            $email,
            $passwordHash,
            $avatar_path,
            Locales::getLocalDateTime(),
            Locales::getLocalDateTime(),
            0,
            MemberStatusEnum::NOTVALIDATED
        );
        return $this->memberRepository->insert($member);
    }

    public function login(string $email, string $password): bool
    {
        $member = $this->memberRepository->findOneByEmail($email);

        if ($member === null) {
            return false;
        }

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

    public function logout(): bool
    {
        unset($_SESSION['id']);
        unset($_SESSION['avatarPath']);
        unset($_SESSION['username']);
        return true;
    }
}
