<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use DateTime;
use Green\TomTroc\Enum\MemberStatusEnum;
use RuntimeException;

class MemberEntity
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

        $this->username = $this->validateField('username', $username);
        $this->email = $this->validateField('email', $email);
        $this->passwordHash = $this->validateField('passwordHash', $passwordHash);
        $this->avatarPath = $this->validateField('avatarPath', $avatarPath);
        $this->createdAt = $this->validateField('createdAt', $createdAt);
        $this->updatedAt = $this->validateField('updatedAt', $updatedAt);
        $this->notificationCount = $this->validateField('notificationCount', $notificationCount);
        $this->status = $this->validateField('status', $status);
    }

    private function validateField(string $property, mixed $field): mixed
    {
        switch ($property) {
            case 'username':
                // A-Z or a-z or 0-9 or _ or - minimum 1 maximum 50
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[a-zA-Z0-9_-]{1,50}$/']]
                );
                $message = 'Username must only contain character in a-z, A-Z, 0-9, _ or -';
                break;

            case 'email':
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_EMAIL
                );
                $message = 'Email is not a valid email';
                break;

            case 'passwordHash':
                // $2y$12$E//8i7U3.5jN0/bHRFPD0ek.1EQjoBXHjbrL0ttB.XwYMA78xpgXu
                // \__/\/ \____________________/\_____________________________/
                //  Alg Cost      Salt                        Hash
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^\$2[aby]?\$\d{1,2}\$[.\/A-Za-z0-9]{53}$/']]
                );
                $message = 'Password Hash is not a valid bcrypt hash';
                break;

            case 'avatarPath':
                // must be in /upload/avatars/ with 1 to 50 a-zA-Z0-9 chars and .png extension
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^\/upload\/avatars\/[a-zA-Z0-9]{1,50}\.png$/']]
                );
                $message = 'Avatar must be stored in /upload/avatars/,' .
                    ' contain only a-z, A-Z or 0-9, and have .png extension';
                break;

            case 'createdAt':
                // must be a date time before now and not before 110 years ago
                $validated = (
                    $field <= new DateTime('now') &&
                    $field > new DateTime('110 years ago')
                );
                $message = 'Creation Date must be before now and afer 110 years ago';
                break;

            case 'updatedAt':
                // must be a date time before now and not before 110 years ago
                $validated = (
                    $field <= new DateTime('now') &&
                    $field > new DateTime('110 years ago')
                );
                $message = 'Updated Date must be before now and afer 110 years ago';
                break;

            case 'status':
                // it is an Enum
                $validated = true;
                break;

            case 'notificationCount':
                // must be a date time before now and not before 110 years ago
                $validated = ($field >= 0);
                $message = 'Notification Count must be >= 0';
                break;

            default:
                throw new RuntimeException('Unknow field passed to the validator');
        }

        if ($validated) {
            return $field;
        } else {
            $message = !isset($message) ? 'Unknown error' : $message;
            throw new RuntimeException("Invalid $property : $message");
        }
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
        $this->username = $this->validateField('username', $username);
    }

    public function setEmail(string $email): void
    {
        $this->email = $this->validateField('email', $email);
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $this->validateField('passwordHash', $passwordHash);
    }

    public function setAvatarPath(string $avatarPath): void
    {
        $this->avatarPath = $this->validateField('avatarPath', $avatarPath);
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $this->validateField('createdAt', $createdAt);
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $this->validateField('updatedAt', $updatedAt);
    }

    public function setStatus(MemberStatusEnum $status): void
    {
        $this->status = $this->validateField('status', $status);
    }

    public function setNotificationCount(int $notificationCount): void
    {
        $this->notificationCount = $this->validateField('notificationCount', $notificationCount);
    }
}
