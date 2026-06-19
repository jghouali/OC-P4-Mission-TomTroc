<?php

declare(strict_types=1);

namespace Green\TomTroc\Manager;

use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use Green\TomTroc\Repository\BookRepository;
use RuntimeException;

class BookManager
{
    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function addBook(
        MemberEntity $member,
        string $title,
        string $author,
        string $imagePath,
        string $description,
        BookStatusEnum $availability
    ): bool {
        $book = new BookEntity(
            $title,
            $author,
            $imagePath,
            $description,
            $availability,
            $member
        );
        return $this->bookRepository->insert($book);
    }

    public function getMyLibrary(): array
    {
        if (isset($_SESSION['id'])) {
            return $this->bookRepository->findAllByMember($_SESSION['id']);
        }
        throw new RuntimeException('$_SESSION[\'id\'] is not set');
    }

    public function listAvailableBook(): array|bool
    {
        $result = $this->bookRepository->findAllByAvailability(BookStatusEnum::AVAILABLE);
        return $result;
    }

    public function getBookDetail(int $bookId): BookEntity|bool
    {
        $result = $this->bookRepository->findById($bookId);
        return $result;
    }
}
