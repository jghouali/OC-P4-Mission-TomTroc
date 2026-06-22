<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PDOException;
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

    public function oneToBook(array $array): BookEntity
    {
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
            $this->memberRepository->findById($array['fk_member_id'])
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

    public function deleteAll(): bool
    {
        return $this->dbManager->deleteAll('books');
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

    public function delete(BookEntity $book): bool
    {
        return $this->dbManager->delete('books', $book->toArray());
    }

    public function findAll(): array
    {
        return $this->arrayToBook(
            $this->dbManager->findAll('books')
        );
    }

    public function findAllWhere(string $column, string $operator, string $value): array
    {
        $columnWhiteList = ['title', 'author', 'description', 'availability', 'fk_member_id'];
        if (in_array($column, $columnWhiteList)) {
            $operatorWhiteList = ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'];

            if (in_array($operator, $operatorWhiteList)) {
                try {
                    $books = $this->dbManager->findAllWhere('books', $column, $operator, $value);
                } catch (PDOException $e) {
                    if (Settings::get(Settings::APP_DEV)) {
                        echo $e->getMessage();
                    }
                    return [];
                }
            } else {
                throw new RuntimeException('Invalid operator');
            }
        } else {
            throw new RuntimeException('Invalid column');
        }

        return $this->arrayToBook($books);
    }

    public function findById(int $id): BookEntity|false
    {
        return $this->oneToBook(
            $this->dbManager->findOne('books', BookEntity::getStorageIdName(), $id)
        );
    }

    public function findByTitle(string $title): BookEntity|null
    {
        $result = $this->dbManager->findOne('books', 'title', $title);

        return $this->oneToBook($result);
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

        return $this->findAllWhere('fk_member_id', '=', (string) $id);
    }

    public function findAllByAvailability(BookStatusEnum $availability): array
    {
        return $this->findAllWhere('availability', '=', $availability->value);
    }

    public function findAllLast(int $count): array
    {
        // 1' ORDER BY book_id DESC LIMIT $count
        // $results = $dbManager->findAllWhere('books', '1', '=', "1' UNION
        // SELECT username, password_hash, username, password_hash, username,
        // password_hash, username FROM members -- ");
        return $this->dbManager->findAllWhere(
            'books',
            '1',
            '=',
            "1' ORDER BY book_id DESC LIMIT $count -- "
        );
    }

    public function update(int $bookId, BookEntity $book): bool
    {
        return $this->dbManager->update('books', $bookId, $book->toArray());
    }
}
