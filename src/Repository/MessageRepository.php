<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use PDOException;

class MessageRepository
{
    // serialize-like this object to server StorageInterface
    public function arrayToMessage(array $array): array
    {
        $result = [];
        foreach ($array as $row) {
            $row[] = new MessageEntity(
                $row['content'],
                $row['sent_at'],
                $row['modified_at'],
                $row['fk_from_member_id'],
                $row['fk_to_member_id'],
                $row['is_read'],
            );
        }

        return $result;
    }

    public function deleteAll(): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->deleteAll('messages');
    }

    public function insert(MessageEntity $message): MessageEntity|false
    {
        $dbManager = Settings::getDbManager();

        $lastId = $dbManager->insert('messages', $message->toArray());
        if (is_int($lastId)) {
            $message->setId($lastId);
            return $message;
        }

        return false;
    }

    public function delete(MessageEntity $message): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->delete('messages', $message->toArray());
    }

    public function findAll(): array
    {
        $dbManager = Settings::getDbManager();
        $messages = $dbManager->findAll('messages');

        return $messages;
    }

    public function findAllWhere(string $column, string $operator, string $value): array
    {
        $dbManager = Settings::getDbManager();

        $messages = [];

        $columnWhiteList = ['content', 'sent_at', 'fk_from_member', 'fk_to_member', 'modified_at', 'is_read'];
        if (in_array($column, $columnWhiteList)) {
            $operatorWhiteList = ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'];
            if (in_array($operator, $operatorWhiteList)) {
                try {
                    $messages = $dbManager->findAllWhere('messages', $column, $operator, $value);
                } catch (PDOException $e) {
                    if (Settings::get(Settings::APP_DEV)) {
                        echo $e->getMessage();
                    }
                    return [];
                }
            }
        }

        return $messages;
    }

    public function findById(int $id): MessageEntity|null
    {
        $dbManager = Settings::getDbManager();
        $result = $dbManager->findOne('messages', MessageEntity::getStorageIdName(), $id);

        if ($result !== []) {
            $message = new MessageEntity(
                $result['content'],
                Locales::getLocalDateTime($result['sent_at']),
                Locales::getLocalDateTime($result['modified_at']),
                Settings::getMemberRepository()->findById($result['fk_from_member_id']),
                Settings::getMemberRepository()->findById($result['fk_to_member_id']),
                MessageStatusEnum::tryFrom($result['is_read']),
            );
            $message->setId($result['message_id']);
        } else {
            $message = null;
        }

        return $message;
    }

    public function findAllByRecipient(int $recipientId): array
    {
        $dbManager = Settings::getDbManager();
        $results = $dbManager->findAllWhere('messages', 'fk_to_member_id', '=', "$recipientId");

        $messagesArray = [];
        foreach ($results as $result) {
            $message = new MessageEntity(
                $result['content'],
                Locales::getLocalDateTime($result['sent_at']),
                Locales::getLocalDateTime($result['modified_at']),
                Settings::getMemberRepository()->findById($result['fk_from_member_id']),
                Settings::getMemberRepository()->findById($result['fk_to_member_id']),
                MessageStatusEnum::tryFrom($result['is_read']),
            );
            $message->setId($result['message_id']);
            $messagesArray[] = $message;
        }

        return $messagesArray;
    }

    public function findAllByMember(int $senderId): array
    {
        $dbManager = Settings::getDbManager();
        $results = $dbManager->findAllWhere(
            'messages',
            "fk_from_member_id = $senderId OR fk_to_member_id",
            '=',
            "$senderId ORDER BY sent_at"
        );

        $messagesArray = [];
        foreach ($results as $result) {
            $message = new MessageEntity(
                $result['content'],
                Locales::getLocalDateTime($result['sent_at']),
                Locales::getLocalDateTime($result['modified_at']),
                Settings::getMemberRepository()->findById($result['fk_from_member_id']),
                Settings::getMemberRepository()->findById($result['fk_to_member_id']),
                MessageStatusEnum::tryFrom($result['is_read']),
            );
            $message->setId($result['message_id']);
            $messagesArray[] = $message;
        }

        return $messagesArray;
    }

    public function findAllBySender(int $senderId): array
    {
        $dbManager = Settings::getDbManager();
        $results = $dbManager->findAllWhere('messages', 'fk_from_member_id', '=', "$senderId");

        $messagesArray = [];
        foreach ($results as $result) {
            $message = new MessageEntity(
                $result['content'],
                Locales::getLocalDateTime($result['sent_at']),
                Locales::getLocalDateTime($result['modified_at']),
                Settings::getMemberRepository()->findById($result['fk_from_member_id']),
                Settings::getMemberRepository()->findById($result['fk_to_member_id']),
                MessageStatusEnum::tryFrom($result['is_read']),
            );
            $message->setId($result['message_id']);
            $messagesArray[] = $message;
        }

        return $messagesArray;
    }

    public function findAllByIsRead(MessageStatusEnum $status): array
    {
        $dbManager = Settings::getDbManager();
        $results = $dbManager->findAllWhere('messages', 'is_read', '=', "$status->value");

        foreach ($results as $result) {
            $message = new MessageEntity(
                $result['content'],
                Locales::getLocalDateTime($result['sent_at']),
                Locales::getLocalDateTime($result['modified_at']),
                Settings::getMemberRepository()->findById($result['fk_from_member_id']),
                Settings::getMemberRepository()->findById($result['fk_to_member_id']),
                MessageStatusEnum::tryFrom($result['is_read']),
            );
            $message->setId($result['message_id']);
        }

        return $results;
    }

    public function update(int $messageId, MessageEntity $message): bool
    {
        $dbManager = Settings::getDbManager();

        return $dbManager->update('messages', $messageId, $message->toArray());
    }
}
