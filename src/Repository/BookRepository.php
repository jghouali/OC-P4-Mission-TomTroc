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
        if ($book->getFromMember()->getId() === null) {
            throw new RuntimeException('Member Id is null');
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
        if ($this->dbManager->update('books', $bookId, $book->toArray())) {
            return $book;
        }
        return false;
    }

    public function delete(BookEntity $book): bool
    {
        return $this->dbManager->delete('books', $book->toArray());
    }

    public function deleteAll(): bool
    {
        return $this->dbManager->deleteAll('books');
    }

    public function findOneById(int $id): BookEntity|false
    {
        return $this->oneToBook(
            $this->dbManager->findOne('books', BookEntity::getStorageIdName(), $id)
        );
    }

    public function findOneByTitle(string $title): BookEntity|null
    {
        $result = $this->dbManager->findOne('books', 'title', $title);

        return $this->oneToBook($result);
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
