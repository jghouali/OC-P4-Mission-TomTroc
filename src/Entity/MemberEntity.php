<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use DateTime;
use Green\TomTroc\Core\Service\ValidatorService;
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

        $this->username = ValidatorService::validateField(
            'username',
            $username,
            ValidatorEnum::textContent50
        );
        $this->email = ValidatorService::validateField(
            'email',
            $email,
            ValidatorEnum::email
        );
        $this->passwordHash = ValidatorService::validateField(
            'passwordHash',
            $passwordHash,
            ValidatorEnum::bcryptHash
        );
        $this->avatarPath = ValidatorService::validateField(
            'avatarPath',
            $avatarPath,
            ValidatorEnum::imagePath
        );
        $this->createdAt = ValidatorService::validateField(
            'createdAt',
            $createdAt,
            ValidatorEnum::humanDate
        );
        $this->updatedAt = ValidatorService::validateField(
            'updatedAt',
            $updatedAt,
            ValidatorEnum::humanDate
        );
        $this->notificationCount = ValidatorService::validateField(
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
            'password_hash' => $this->passwordHash,
            'avatar_path' => $this->avatarPath,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'notification_count' => "$this->notificationCount",
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

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt->format('Y-m-d H:i:s');
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
        $this->username = ValidatorService::validateField(
            'username',
            $username,
            ValidatorEnum::textContent50
        );
    }

    public function setEmail(string $email): void
    {
        $this->email = ValidatorService::validateField(
            'email',
            $email,
            ValidatorEnum::email
        );
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = ValidatorService::validateField(
            'passwordHash',
            $passwordHash,
            ValidatorEnum::bcryptHash
        );
    }

    public function setAvatarPath(string $avatarPath): void
    {
        $this->avatarPath = ValidatorService::validateField(
            'avatarPath',
            $avatarPath,
            ValidatorEnum::imagePath
        );
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = ValidatorService::validateField(
            'createdAt',
            $createdAt,
            ValidatorEnum::humanDate
        );
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = ValidatorService::validateField(
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
        $this->notificationCount = ValidatorService::validateField(
            'notificationCount',
            $notificationCount,
            ValidatorEnum::intCounter
        );
    }
}
