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

    public function listBooks(?string $search = ''): array|bool
    {
        $result = $this->bookRepository->findAllFilter($search);
        return $result;
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

    public function getBookDetail(BookEntity|int $book): BookEntity|false
    {
        if (is_int($book)) {
            $bookId = $book;
        } else {
            $bookId = $book->getId();
        }
        $result = $this->bookRepository->findOneById($bookId);
        if ($result === null) {
            return false;
        }
        return $result;
    }

    public function updateBook(BookEntity $book): BookEntity|false
    {
        $loggedMember = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedMember === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($loggedMember::class === 'Green\TomTroc\Entity\MemberEntity') {
            $bookId = $book->getId();
            if (is_int($bookId)) {
                return $this->bookRepository->update($bookId, $book);
            }
            return false;
        } else {
            throw new RuntimeException('Unknown Error', 500);
        }
    }

    public function deleteBook(BookEntity $book): bool
    {
        $loggedMember = $this->authentificationService->getCurrentLoggedMember();
        if ($loggedMember === null) {
            throw new RuntimeException('You are not logged in');
        } elseif ($loggedMember::class === 'Green\TomTroc\Entity\MemberEntity') {
            $bookId = $book->getId();
            if (is_int($bookId)) {
                return $this->bookRepository->delete($book);
            }
            return false;
        } else {
            throw new RuntimeException('Unknown Error', 500);
        }
    }
}
