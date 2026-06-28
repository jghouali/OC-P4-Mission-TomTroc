<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use RuntimeException;

class MemberRepository
{
    private StorageInterface $dbManager;

    public function __construct(StorageInterface $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    public function oneToMember(array $array): MemberEntity
    {
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
        $lastId = $this->dbManager->insert('members', $member->toArray());
        if (is_int($lastId)) {
            $member->setId($lastId);
        }
        return $member;
    }

    public function update(int $memberId, MemberEntity $member): MemberEntity|false
    {
        $member->setUpdatedAt(Locales::getLocalDateTime());
        $result = $this->dbManager->update('members', $memberId, $member->toArray());

        if ($result) {
            $memberUpdated = $this->findOneById($memberId);
            return $memberUpdated;
        }

        return $result;
    }

    public function delete(MemberEntity $member): bool
    {
        return $this->dbManager->delete('members', $member->toArray());
    }

    public function deleteAll(): bool
    {
        return $this->dbManager->deleteAll('members');
    }

    public function findOneById(int $id): MemberEntity
    {
        $result = $this->dbManager->findOne('members', MemberEntity::getStorageIdName(), $id);

        $member = $this->oneToMember($result);

        return $member;
    }

    public function findOneByUsername(string $username): MemberEntity
    {
        $result = $this->dbManager->findOne('members', 'username', $username);

        if (count($result) === 0) {
            throw new RuntimeException("User $username doe not exist");
        }

        $member = $this->oneToMember($result);

        return $member;
    }

    public function findOneByEmail(string $email): MemberEntity|null
    {
        $result = $this->dbManager->findOne('members', 'email', $email);

        if (count($result) === 0) {
            return null;
        }

        return $this->oneToMember($result);
    }

    public function findAll(): array
    {
        return $this->arrayToMember($this->dbManager->findAll('members'));
    }
}
