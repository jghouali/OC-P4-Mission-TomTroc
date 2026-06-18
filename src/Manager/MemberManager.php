<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use Green\TomTroc\Enum\MemberStatusEnum;
use RuntimeException;

class MemberManager
{
    public function register(string $username, string $email, string $password, string $avatar_path): bool
    {
        $passwordHash = password_hash(
            $password,
            Settings::get(Settings::APP_SECURITY_HASH_ALGO, PASSWORD_DEFAULT)
        );
        Settings::getMemberRepository()->insert(
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
        return true;
    }

    public function login(string $email, string $password): bool
    {
        $member = Settings::getMemberRepository()->findByEmail($email);
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

    public function modifyProfile(int $id, string $username, string $email, string $password, string $avatarPath): bool
    {
        if ($_SESSION['id'] == $id) {
            $member = Settings::getMemberRepository()->findById($id);
            $member->setUserName($username);
            $member->setEmail($email);
            $member->setPasswordHash(password_hash($password, Settings::get(Settings::APP_SECURITY_HASH_ALGO)));
            $member->setAvatarPath($avatarPath);
            Settings::getMemberRepository()->update($id, $member);
            return true;
        }
        return false;
    }

    public function addBook(
        MemberEntity $user,
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability
    ): bool {
        if ($_SESSION['id'] == $user->getId()) {
            $book = new BookEntity(
                $title,
                $author,
                $imagePath,
                $description,
                $availability,
                $user
            );
            Settings::getBookRepository()->insert($book);
            return true;
        }
        return false;
    }

    public function getMyLibrary(
        MemberEntity $member
    ): array|bool {
        if ($_SESSION['id'] == $member->getId()) {
            $result = Settings::getBookRepository()->findAllByMember($member);
            return $result;
        }
        return false;
    }

    public function memberExist(string $email): bool
    {
        return (
            Settings::getMemberRepository()
                ->findByEmail($email)::class === 'Green\TomTroc\Entity\MemberEntity'
        );
    }

    public function memberExistAndValidated(string $email): bool
    {
        return (
            Settings::getMemberRepository()
            ->findByEmail($email)
            ->getStatus() === MemberStatusEnum::VALIDATED
        );
    }

    public function getProfileData(string $username): array
    {
        $member = Settings::getMemberRepository()->findByUsername($username);
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
