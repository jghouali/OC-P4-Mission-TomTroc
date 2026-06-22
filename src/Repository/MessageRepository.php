<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use PDOException;
use RuntimeException;

class MessageRepository
{
    private StorageInterface $dbManager;
    private MemberRepository $memberRepository;

    public function __construct(StorageInterface $dbManager, MemberRepository $memberRepository)
    {
        $this->dbManager = $dbManager;
        $this->memberRepository = $memberRepository;
    }

    // serialize-like this object to server StorageInterface
    public function oneToMessage(array $array): MessageEntity
    {
        $message = new MessageEntity(
            $array['content'],
            Locales::getLocalDateTime($array['sent_at']),
            Locales::getLocalDateTime($array['modified_at']),
            $this->memberRepository->findById($array['fk_from_member_id']),
            $this->memberRepository->findById($array['fk_to_member_id']),
            MessageStatusEnum::tryFrom($array['is_read']),
        );
        $message->setId($array['message_id']);

        return $message;
    }

    public function arrayToMessage(array $array): array
    {
        $results = [];
        foreach ($array as $row) {
            $message = $this->oneToMessage($row);

            $results[] = $message;
        }

        return $results;
    }

    public function deleteAll(): bool
    {
        return $this->dbManager->deleteAll('messages');
    }

    public function insert(MessageEntity $message): MessageEntity|false
    {
        $lastId = $this->dbManager->insert('messages', $message->toArray());
        if (is_int($lastId)) {
            $message->setId($lastId);
            return $message;
        }

        return false;
    }

    public function delete(MessageEntity $message): bool
    {
        return $this->dbManager->delete('messages', $message->toArray());
    }

    public function findAll(): array
    {
        $messages = $this->arrayToMessage(
            $this->dbManager->findAll('messages')
        );

        return $messages;
    }

    public function findAllWhere(string $column, string $operator, string $value): array
    {
        $messages = [];

        $columnWhiteList = ['content', 'sent_at', 'fk_from_member_id', 'fk_to_member_id', 'modified_at', 'is_read'];
        if (in_array($column, $columnWhiteList)) {
            $operatorWhiteList = ['=', '>', '<', '>=', '<=', 'LIKE', 'ILIKE'];
            if (in_array($operator, $operatorWhiteList)) {
                try {
                    $messages = $this->dbManager->findAllWhere('messages', $column, $operator, $value);
                } catch (PDOException $e) {
                    if (Settings::get(Settings::APP_DEV)) {
                        echo $e->getMessage();
                    }
                    // return [];
                    echo $e->getMessage();
                }
            } else {
                throw new RuntimeException('Invalid operator');
            }
        } else {
            throw new RuntimeException('Invalid column');
        }

        return $this->arrayToMessage($messages);
    }

    public function findById(int $id): MessageEntity|null
    {
        $result = $this->dbManager->findOne('messages', MessageEntity::getStorageIdName(), $id);

        if ($result !== []) {
            $message = $this->oneToMessage($result);
            $message->setId($result['message_id']);
        } else {
            $message = null;
        }

        return $message;
    }

    public function findAllByRecipient(int|MemberEntity $recipient): array
    {
        if (is_int($recipient)) {
            $id = $recipient;
        } else {
            $id = $recipient->getId();
        }

        return $this->findAllWhere('fk_to_member_id', '=', "$id");
    }

    public function findAllByMember(int|MemberEntity $member): array
    {
        if (is_int($member)) {
            $id = $member;
        } else {
            $id = $member->getId();
        }
        return $this->dbManager->findAllWhere(
            'messages',
            "fk_from_member_id = $id OR fk_to_member_id",
            '=',
            "$id ORDER BY sent_at"
        );
    }

    public function findAllBySender(int|MemberEntity $sender): array
    {
        if (is_int($sender)) {
            $id = $sender;
        } else {
            $id = $sender->getId();
        }
        return $this->findAllWhere(
            'fk_from_member_id',
            '=',
            "$id"
        );
    }

    public function findAllByIsRead(MessageStatusEnum $status): array
    {
        return $this->findAllWhere(
            'is_read',
            '=',
            "$status->value"
        );
    }

    public function update(int $messageId, MessageEntity $message): bool
    {
        return $this->dbManager->update('messages', $messageId, $message->toArray());
    }
}
