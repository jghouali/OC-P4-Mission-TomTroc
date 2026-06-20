<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Core\Service\AuthentificationService;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use Green\TomTroc\Repository\BookRepository;
use RuntimeException;

class BookManager
{
    private BookRepository $bookRepository;
    private AuthentificationService $authentificationService;

    public function __construct(BookRepository $bookRepository, AuthentificationService $authentificationService)
    {
        $this->bookRepository = $bookRepository;
        $this->authentificationService = $authentificationService;
    }

    public function addBook(
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability
    ): BookEntity|false {
        $loggedMember = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedMember === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($loggedMember::class === 'Green\TomTroc\Entity\MemberEntity') {
            $book = new BookEntity(
                $title,
                $author,
                $imagePath,
                $description,
                $availability,
                $loggedMember
            );
            return $this->bookRepository->insert($book);
        } else {
            throw new RuntimeException('Unknown error');
        }
    }

    public function getMyLibrary(): array
    {
        $member = $this->authentificationService->getCurrentLoggedMember();
        if ($member === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($member::class === 'Green\TomTroc\Entity\MemberEntity') {
            return $this->bookRepository->findAllByMember($member);
        } else {
            throw new RuntimeException('Unknown error');
        }
    }

    public function listAvailableBook(): array|bool
    {
        $result = $this->bookRepository->findAllByAvailability(BookStatusEnum::AVAILABLE);
        return $result;
    }

    public function listLastBook(int $count): array|bool
    {
        $result = $this->bookRepository->findAllLast($count);
        return $result;
    }

    public function getBookDetail(BookEntity|int $book): BookEntity|bool
    {
        if (is_int($book)) {
            $bookId = $book;
        } else {
            $bookId = $book->getId();
        }
        return $this->bookRepository->findById($bookId);
    }
}
