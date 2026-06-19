<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use PDOException;
use RuntimeException;

class MemberRepository
{
    // serialize-like this object to server StorageInterface
    public function arrayToMember(array $array): array
    {
        $result = [];
        foreach ($array as $row) {
            $result[] = new MemberEntity(
                $row['username'],
                $row['email'],
                $row['password_hash'],
                $row['avatar_path'],
                Locales::getLocalDateTime($row['created_at']),
                Locales::getLocalDateTime($row['updated_at']),
                $row['notification_count'],
                MemberStatusEnum::tryFrom($row['status'])
            );
        }

        return $result;
    }

    public function deleteAll(): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->deleteAll('members');
    }

    public function insert(MemberEntity $member): int|false
    {
        $dbManager = Settings::getDbManager();

        $lastId = $dbManager->insert('members', $member->toArray());
        if (is_int($lastId)) {
            $member->setId($lastId);
        }
        return $lastId;
    }

    public function delete(MemberEntity $member): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->delete('members', $member->toArray());
    }

    public function findAll(): array
    {
        $dbManager = Settings::getDbManager();
        $members = $dbManager->findAll('members');

        return $members;
    }

    public function findAllWhere(string $column, string $operator, string $value): array
    {
        $dbManager = Settings::getDbManager();

        $members = [];

        $columnWhiteList = ['username', 'email'];
        if (in_array($column, $columnWhiteList)) {
            $operatorWhiteList = ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'];
            if (in_array($operator, $operatorWhiteList)) {
                try {
                    $members = $dbManager->findAllWhere('members', $column, $operator, $value);
                } catch (PDOException $e) {
                    if (Settings::get(Settings::APP_DEV)) {
                        echo $e->getMessage();
                    }
                    return [];
                }
            }
        }

        return $members;
    }

    public function findById(int $id): MemberEntity
    {
        $dbManager = Settings::getDbManager();
        $result = $dbManager->findOne('members', MemberEntity::getStorageIdName(), $id);

        $member = new MemberEntity(
            $result['username'],
            $result['email'],
            $result['password_hash'],
            $result['avatar_path'],
            Locales::getLocalDateTime($result['created_at']),
            Locales::getLocalDateTime($result['updated_at']),
            $result['notification_count'],
            MemberStatusEnum::tryFrom($result['status'])
        );
        $member->setId($result['member_id']);

        return $member;
    }

    public function findByUsername(string $username): MemberEntity
    {
        $dbManager = Settings::getDbManager();
        $result = $dbManager->findOne('members', 'username', $username);

        if (count($result) === 0) {
            throw new RuntimeException("User $username doe not exist");
        }

        $member = new MemberEntity(
            $result['username'],
            $result['email'],
            $result['password_hash'],
            $result['avatar_path'],
            Locales::getLocalDateTime($result['created_at']),
            Locales::getLocalDateTime($result['updated_at']),
            $result['notification_count'],
            MemberStatusEnum::tryFrom($result['status'])
        );
        $member->setId($result['member_id']);

        return $member;
    }

    public function findByEmail(string $email): MemberEntity
    {
        $dbManager = Settings::getDbManager();
        $result = $dbManager->findOne('members', 'email', $email);

        if (count($result) === 0) {
            throw new RuntimeException("User $email doe not exist");
        }

        $member = new MemberEntity(
            $result['username'],
            $result['email'],
            $result['password_hash'],
            $result['avatar_path'],
            Locales::getLocalDateTime($result['created_at']),
            Locales::getLocalDateTime($result['updated_at']),
            $result['notification_count'],
            MemberStatusEnum::tryFrom($result['status'])
        );
        $member->setId($result['member_id']);

        return $member;
    }

    public function update(int $memberId, MemberEntity $member): MemberEntity|false
    {
        $dbManager = Settings::getDbManager();

        $member->setUpdatedAt(Locales::getLocalDateTime());
        $result = $dbManager->update('members', $memberId, $member->toArray());

        if ($result) {
            $memberUpdated = $this->findById($memberId);
            return $memberUpdated;
        }

        return $result;
    }
}
