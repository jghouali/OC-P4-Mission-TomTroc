<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Enum\MemberStatusEnum;
use PDOException;

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
                date_create($row['created_at']),
                date_create($row['updated_at']),
                $row['notification_count'],
                MemberStatusEnum::tryFrom($row['status'])
            );
        }

        return $result;
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
            date_create($result['created_at']),
            date_create($result['updated_at']),
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

        $member = new MemberEntity(
            $result['username'],
            $result['email'],
            $result['password_hash'],
            $result['avatar_path'],
            date_create($result['created_at']),
            date_create($result['updated_at']),
            $result['notification_count'],
            MemberStatusEnum::tryFrom($result['status'])
        );
        $member->setId($result['member_id']);

        return $member;
    }

    public function insert(MemberEntity $member): bool
    {
        $dbManager = Settings::getDbManager();

        $lastId = $dbManager->insert('members', $member->toArray());
        if (is_int($lastId)) {
            $member->setId($lastId);
            return true;
        }

        return false;
    }

    public function delete(MemberEntity $member): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->delete('members', $member->toArray());
    }

    public function deleteAll(): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->deleteAll('members');
    }

    public function update(int $memberId, MemberEntity $member): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->update('members', $memberId, $member->toArray());
    }
}
