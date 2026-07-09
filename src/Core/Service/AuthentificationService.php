<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\Service;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use Green\TomTroc\Enum\ValidatorEnum;
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
        if (ValidatorService::validateField('password', $password, ValidatorEnum::clearPassword)) {
            return password_hash(
                $password,
                $this->passwordHashAlgorithm
            );
        }
        throw new RuntimeException(
            'Password must contain between 12 and 72 character and' .
                ' at least one [a-z], one [0-9], one [!@#$%^&*()_\-+=.?]'
        );
    }

    public function emailAlreadyRegistered(string $email): bool
    {
        if ($this->memberRepository->findOneByEmail($email) === null) {
            return false;
        }
        return true;
    }

    public function usernameAlreadyRegistered(string $username): bool
    {
        if ($this->memberRepository->findOneByUsername($username) === null) {
            return false;
        }
        return true;
    }

    public function memberExistAndValidated(string $email): bool
    {
        return (
            $this->memberRepository
            ->findOneByEmail($email)
            ->getStatus() === MemberStatusEnum::VALIDATED
        );
    }

    public function register(string $username, string $email, string $password, string $avatar_path): MemberEntity|false
    {
        if ($this->memberRepository->findOneByEmail($email) !== null) {
            throw new RuntimeException('email already registered', 400);
        }
        if ($this->memberRepository->findOneByUsername($username) !== null) {
            throw new RuntimeException('username already registered', 400);
        }
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

        if ($member !== null) {
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
            throw new RuntimeException('failed to retrieve hashPassword', 500);
        }
        return false;
    }

    public function logout(): bool
    {
        unset($_SESSION['id']);
        unset($_SESSION['avatarPath']);
        unset($_SESSION['username']);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        return true;
    }
}
