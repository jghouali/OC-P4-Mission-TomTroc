<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use DateTime;
use Green\TomTroc\Enum\MemberStatusEnum;
use Green\TomTroc\Enum\ValidatorEnum;

class MemberEntity extends AbstractEntity implements EntityInterface
{
    private string $username;
    private string $email;
    private string $passwordHash;
    private string $avatarPath;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private int $notificationCount;
    private MemberStatusEnum $status;

    public function __construct(
        string $username,
        string $email,
        string $passwordHash,
        string $avatarPath,
        DateTime $createdAt,
        DateTime $updatedAt,
        int $notificationCount,
        MemberStatusEnum $status
    ) {

        $this->username = $this->validateField(
            'username',
            $username,
            ValidatorEnum::alphanumeric_50
        );
        $this->email = $this->validateField(
            'email',
            $email,
            ValidatorEnum::email
        );
        $this->passwordHash = $this->validateField(
            'passwordHash',
            $passwordHash,
            ValidatorEnum::bcryptHash
        );
        $this->avatarPath = $this->validateField(
            'avatarPath',
            $avatarPath,
            ValidatorEnum::uploadFile
        );
        $this->createdAt = $this->validateField(
            'createdAt',
            $createdAt,
            ValidatorEnum::humanDate
        );
        $this->updatedAt = $this->validateField(
            'updatedAt',
            $updatedAt,
            ValidatorEnum::humanDate
        );
        $this->notificationCount = $this->validateField(
            'notificationCount',
            $notificationCount,
            ValidatorEnum::intCounter
        );
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'passwordHash' => $this->passwordHash,
            'avatarPath' => $this->avatarPath,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'notificationCount' => "$this->notificationCount",
            'status' => $this->status->value,
            $this->getStorageIdName() => $this->getId(),
        ];
    }

    public static function getStorageIdName(): string
    {
        return 'member_id';
    }

    public function getUserName(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getAvatarPath(): string
    {
        return $this->avatarPath;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getStatus(): MemberStatusEnum
    {
        return $this->status;
    }

    public function getNotificationCount(): int
    {
        return $this->notificationCount;
    }

    public function setUserName(string $username): void
    {
        $this->username = $this->validateField(
            'username',
            $username,
            ValidatorEnum::alphanumeric_50
        );
    }

    public function setEmail(string $email): void
    {
        $this->email = $this->validateField(
            'email',
            $email,
            ValidatorEnum::email
        );
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $this->validateField(
            'passwordHash',
            $passwordHash,
            ValidatorEnum::bcryptHash
        );
    }

    public function setAvatarPath(string $avatarPath): void
    {
        $this->avatarPath = $this->validateField(
            'avatarPath',
            $avatarPath,
            ValidatorEnum::uploadFile
        );
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $this->validateField(
            'createdAt',
            $createdAt,
            ValidatorEnum::humanDate
        );
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $this->validateField(
            'updatedAt',
            $updatedAt,
            ValidatorEnum::humanDate
        );
    }

    public function setStatus(MemberStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function setNotificationCount(int $notificationCount): void
    {
        $this->notificationCount = $this->validateField(
            'notificationCount',
            $notificationCount,
            ValidatorEnum::intCounter
        );
    }
}
