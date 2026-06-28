<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use DateTime;
use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Repository\BookRepository;

class ProfileEntity
{
    private BookRepository $bookRepository;
    private string $username;
    private string $email;
    private string $avatarPath;
    private string $memberSince;
    private array $books;
    private string $bookCount;

    public function __construct(MemberEntity $member)
    {
        $this->bookRepository = Settings::getBookRepository();
        $this->username = $member->getUserName();
        $this->email = $member->getEmail();
        $this->avatarPath = $member->getAvatarPath();
        $this->memberSince = $this->memberSince(Locales::getLocalDateTime($member->getCreatedAt()));
        $this->books = $this->bookRepository->findAllByMember($member);
        $this->bookCount = $this->bookCount(count($this->books));
    }

    public function memberSince(DateTime $createdAt): string
    {
        $result = (int) $createdAt->diff(Locales::getLocalDateTime())->format('%a');
        if ($result === 0) {
            return $createdAt->diff(Locales::getLocalDateTime())->format('aujourd\'hui');
        } elseif ($result === 1) {
            return $createdAt->diff(Locales::getLocalDateTime())->format('%d jour');
        } elseif ($result <= 30) {
            return $createdAt->diff(Locales::getLocalDateTime())->format('%d jours');
        } elseif ($result <= 365) {
            return $createdAt->diff(Locales::getLocalDateTime())->format('%m mois');
        } elseif ($result <= 730) {
            return $createdAt->diff(Locales::getLocalDateTime())->format('%y an');
        } else {
            return $createdAt->diff(Locales::getLocalDateTime())->format('%y ans');
        }
    }

    public function bookCount(int $bookCount): string
    {
        if ($bookCount <= 1) {
            return "$bookCount livre";
        } else {
            return "$bookCount livres";
        }
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAvatarPath(): string
    {
        return $this->avatarPath;
    }

    public function getMemberSince(): string
    {
        return $this->memberSince;
    }

    public function getBooks(): array
    {
        return $this->books;
    }

    public function getBookCount(): string
    {
        return $this->bookCount;
    }
}
