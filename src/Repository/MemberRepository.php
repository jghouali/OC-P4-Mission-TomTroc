<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use PDO;
use RuntimeException;

class MemberRepository
{
    private StorageInterface $dbManager;

    public function __construct(StorageInterface $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function oneToMember(array $array): MemberEntity|false
    {
        if ($array === []) {
            return false;
        }

        $member = new MemberEntity(
            $array['username'],
            $array['email'],
            $array['password_hash'],
            $array['avatar_path'],
            Locales::getLocalDateTime($array['created_at']),
            Locales::getLocalDateTime($array['updated_at']),
            $array['notification_count'],
            MemberStatusEnum::tryFrom($array['status'])
        );
        $member->setId($array['member_id']);

        return $member;
    }

    // serialize-like this object to server StorageInterface
    public function arrayToMember(array $array): array
    {
        $results = [];
        foreach ($array as $row) {
            $message = $this->oneToMember($row);

            $results[] = $message;
        }

        return $results;
    }

    public function insert(MemberEntity $member): MemberEntity|false
    {
        $memberId = $member->getId();
        if (is_int($memberId) && $memberId > 0) {
            throw new RuntimeException('This member already inserted', 400);
        }

        $lastId = $this->dbManager->insert('members', $member->toArray());
        if (is_int($lastId)) {
            $member->setId($lastId);
            return $member;
        }
        return false;
    }

    public function update(int $memberId, MemberEntity $member): MemberEntity|false
    {
        if ($member->getId() !== null && $member->getId() !== $memberId) {
            throw new RuntimeException('memberId mismatch with memberId whithin the given MemberEntity', 400);
        }

        if (!$this->findOneById($memberId)) {
            throw new RuntimeException('memberId doesnt exist', 400);
        }

        $member->setUpdatedAt(Locales::getLocalDateTime());
        $result = $this->dbManager->update('members', $memberId, $member->toArray());

        if ($result === 1) {
            $member->setId($memberId);
            return $member;
        } elseif ($result === 0) {
            return false;
        }
        throw new RuntimeException('More than 1 entry updated', 500);
    }

    public function delete(MemberEntity $member): bool
    {
        if ($member->getId() === null) {
            throw new RuntimeException('memberId is null', 400);
        }

        if (!$this->findOneById($member->getId())) {
            throw new RuntimeException('memberId doesnt exist', 400);
        }

        $result = $this->dbManager->delete('members', $member->toArray());

        if ($result === 1) {
            return true;
        } elseif ($result === 0) {
            return false;
        }
        throw new RuntimeException('More than 1 entry deleted', 500);
    }

    public function deleteAll(): bool
    {
        return $this->dbManager->deleteAll('members');
    }

    public function findOneById(int $id): MemberEntity|null
    {
        $primary_key = MemberEntity::getStorageIdName();

        $result = $this->dbManager->queryCustom(
            "SELECT *
            FROM members
            WHERE $primary_key = :member_id",
            [
                'member_id' => [$id, PDO::PARAM_INT,],
            ]
        );

        if (count($result) === 1) {
            return $this->oneToMember($result[0]);
        }
        return null;
    }

    public function findOneByUsername(string $username): MemberEntity|null
    {
        $result = $this->dbManager->queryCustom(
            'SELECT *
            FROM members
            WHERE username = :username',
            [
                'username' => [$username, PDO::PARAM_STR,],
            ]
        );

        if (count($result) === 0) {
            return null;
        }

        return $this->oneToMember($result[0]);
    }

    public function findOneByEmail(string $email): MemberEntity|null
    {
        $result = $this->dbManager->queryCustom(
            'SELECT *
            FROM members
            WHERE email = :email',
            [
                'email' => [$email, PDO::PARAM_STR,],
            ]
        );

        if (count($result) === 0) {
            return null;
        }

        return $this->oneToMember($result[0]);
    }

    public function findAll(): array
    {
        return $this->arrayToMember($this->dbManager->findAll('members'));
    }
}
