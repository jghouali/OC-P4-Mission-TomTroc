<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\BookEntity;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\BookStatusEnum;
use PDOException;
use RuntimeException;

class BookRepository
{
    // serialize-like this object to server StorageInterface
    public function arrayToBook(array $array): array
    {
        $result = [];
        foreach ($array as $row) {
            $row[] = new BookEntity(
                $row['title'],
                $row['author'],
                $row['image_path'],
                $row['description'],
                BookStatusEnum::tryFrom($row['availability']),
                $row['fk_member_id']
            );
        }

        return $result;
    }

    public function deleteAll(): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->deleteAll('books');
    }

    public function insert(BookEntity $book): bool
    {
        if ($book->getFromMember()->getId() === null) {
            throw new RuntimeException('Member Id is null');
        }

        $dbManager = Settings::getDbManager();

        $lastId = $dbManager->insert('books', $book->toArray());
        if (is_int($lastId)) {
            $book->setId($lastId);

            return true;
        }

        return false;
    }

    public function delete(BookEntity $book): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->delete('books', $book->toArray());
    }

    public function findAll(): array
    {
        $dbManager = Settings::getDbManager();
        $books = $dbManager->findAll('books');

        return $books;
    }

    public function findAllWhere(string $column, string $operator, string $value): array
    {
        $booksArray = [];

        $columnWhiteList = ['title', 'author', 'description', 'availability', 'fk_member_id'];
        if (in_array($column, $columnWhiteList)) {
            $operatorWhiteList = ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'];

            if (in_array($operator, $operatorWhiteList)) {
                try {
                    $books = Settings::getDbManager()->findAllWhere('books', $column, $operator, $value);

                    foreach ($books as $book) {
                        $bookObject = new BookEntity(
                            $book['title'],
                            $book['author'],
                            $book['image_path'],
                            $book['description'],
                            BookStatusEnum::tryFrom($book['availability']),
                            $book['fk_member_id']
                        );
                        $bookObject->setId($book['book_id']);

                        $booksArray[] = $bookObject;
                    }
                } catch (PDOException $e) {
                    if (Settings::get(Settings::APP_DEV)) {
                        echo $e->getMessage();
                    }
                    return [];
                }
            }
        }

        return $booksArray;
    }

    public function findById(int $id): BookEntity
    {
        $dbManager = Settings::getDbManager();
        $result = $dbManager->findOne('books', BookEntity::getStorageIdName(), $id);

        $book = new BookEntity(
            $result['title'],
            $result['author'],
            $result['image_path'],
            $result['description'],
            BookStatusEnum::tryFrom($result['availability']),
            Settings::getMemberRepository()->findById($result['fk_member_id'])
        );
        $book->setId($result['book_id']);

        return $book;
    }

    public function findByTitle(string $title): BookEntity|null
    {
        try {
            $bookArray = Settings::getDbManager()->findOne('books', 'title', $title);

            $book = new BookEntity(
                $bookArray['title'],
                $bookArray['author'],
                $bookArray['image_path'],
                $bookArray['description'],
                BookStatusEnum::tryFrom($bookArray['availability']),
                Settings::getMemberRepository()->findById($bookArray['fk_member_id']),
            );
            $book->setId($bookArray['book_id']);
        } catch (PDOException $e) {
            if (Settings::get(Settings::APP_DEV)) {
                echo $e->getMessage();
            }
            return null;
        }

        return $book;
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
        return Settings::getDbManager()->findAllWhere('books', 'fk_member_id', '=', (string) $id);
    }

    public function findAllByAvailability(BookStatusEnum $availability): array
    {
        $dbManager = Settings::getDbManager();
        $results = $dbManager->findAllWhere('books', 'availability', '=', $availability->value);

        return $results;
    }

    public function update(int $bookId, BookEntity $book): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->update('books', $bookId, $book->toArray());
    }
}
