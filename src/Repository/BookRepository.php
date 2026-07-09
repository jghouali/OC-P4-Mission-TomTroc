<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PDO;
use RuntimeException;

class BookRepository
{
    private StorageInterface $dbManager;
    private MemberRepository $memberRepository;

    public function __construct(StorageInterface $dbManager, MemberRepository $memberRepository)
    {
        $this->dbManager = $dbManager;
        $this->memberRepository = $memberRepository;
    }
    // serialize-like this object to server StorageInterface

    public function oneToBook(array $array): BookEntity|false
    {
        if ($array === []) {
            return false;
        }

        $book = new BookEntity(
            $array['title'],
            $array['author'],
            $array['image_path'],
            $array['description'],
            BookStatusEnum::tryFrom($array['availability']),
            $array['fk_member_id']
        );
        $book->setId($array['book_id']);
        $book->setFromMember(
            $this->memberRepository->findOneById($array['fk_member_id'])
        );

        return $book;
    }

    public function arrayToBook(array $array): array
    {
        $results = [];
        foreach ($array as $row) {
            $message = $this->oneToBook($row);

            $results[] = $message;
        }

        return $results;
    }

    public function insert(BookEntity $book): BookEntity|false
    {
        $bookId = $book->getId();
        if (is_int($bookId) && $bookId > 0) {
            throw new RuntimeException('This book already inserted', 400);
        }

        if ($book->getFromMember()->getId() === null) {
            throw new RuntimeException('Member Id is null', 400);
        }

        $lastId = $this->dbManager->insert('books', $book->toArray());
        if (is_int($lastId)) {
            $book->setId($lastId);
            return $book;
        }

        return false;
    }

    public function update(int $bookId, BookEntity $book): BookEntity|false
    {
        if ($book->getId() !== null && $book->getId() !== $bookId) {
            throw new RuntimeException('bookId mismatch with bookId whithin the given BookEntity', 400);
        }

        if ($book->getFromMember()->getId() === null) {
            throw new RuntimeException('Null memberId in the given FromMember', 400);
        }

        if (!$this->findOneById($bookId)) {
            throw new RuntimeException('bookId doesnt exist', 400);
        }

        $result = $this->dbManager->update('books', $bookId, $book->toArray());
        if ($result === 1) {
            $book->setId($bookId);
            return $book;
        } elseif ($result === 0) {
            return false;
        }
        throw new RuntimeException('More than 1 entry updated', 500);
    }

    public function delete(BookEntity $book): bool
    {
        if ($book->getId() === null) {
            throw new RuntimeException('bookId is null', 400);
        }

        if (!$this->findOneById($book->getId())) {
            throw new RuntimeException('bookId doesnt exist', 400);
        }

        $result = $this->dbManager->delete('books', $book->toArray());

        if ($result === 1) {
            return true;
        } elseif ($result === 0) {
            return false;
        }
        throw new RuntimeException('More than 1 entry deleted', 500);
    }

    public function deleteAll(): bool
    {
        return $this->dbManager->deleteAll('books');
    }

    public function findOneById(int $id): BookEntity|null
    {
        $primary_key = BookEntity::getStorageIdName();

        $result = $this->dbManager->queryCustom(
            "SELECT *
            FROM books
            WHERE $primary_key = :book_id",
            [
                'book_id' => [$id, PDO::PARAM_INT,],
            ]
        );

        if (count($result) === 1) {
            return $this->oneToBook($result[0]);
        }
        return null;
    }

    public function findAllByTitle(string $title): array
    {
        $result = $this->dbManager->queryCustom(
            'SELECT *
            FROM books
            WHERE title = :title',
            [
                'title' => [$title, PDO::PARAM_STR,],
            ]
        );

        if (count($result) === 0) {
            return [];
        }
        return $this->arrayToBook($result);
    }

    public function findAll(): array
    {
        return $this->arrayToBook(
            $this->dbManager->findAll('books')
        );
    }

    public function findAllByMember(int|MemberEntity $member): array
    {
        if (is_int($member)) {
            $id = $member;
        } elseif ($member::class === 'Green\TomTroc\Entity\MemberEntity') {
            $id = $member->getId();
        } else {
            throw new RuntimeException("$member is neither int or MemberEntity");
        }

        $results = $this->dbManager->queryCustom(
            'SELECT *
            FROM books
            WHERE fk_member_id = :member_id',
            [
                'member_id' => [$id, PDO::PARAM_INT],
            ]
        );

        return $this->arrayToBook($results);
    }

    public function findAllByAvailability(BookStatusEnum $availability): array
    {
        $results = $this->dbManager->queryCustom(
            'SELECT *
            FROM books
            WHERE availability = :availability',
            [
                'availability' => [$availability->value, PDO::PARAM_STR],
            ]
        );

        return $this->arrayToBook($results);
    }

    public function findAllLast(int $count): array
    {
        $results = $this->dbManager->queryCustom(
            'SELECT *
            FROM books
            ORDER BY book_id DESC
            LIMIT :count',
            [
                'count' => [$count, PDO::PARAM_INT],
            ]
        );

        return $this->arrayToBook($results);
    }

    public function findAllFilter(string $search): array
    {
        $results = $this->dbManager->queryCustom(
            'SELECT *
            FROM books
            WHERE title LIKE :search
            OR author LIKE :search
            ORDER BY book_id DESC',
            [
                'search' => ["%$search%", PDO::PARAM_STR],
            ]
        );

        return $this->arrayToBook($results);
    }
}
