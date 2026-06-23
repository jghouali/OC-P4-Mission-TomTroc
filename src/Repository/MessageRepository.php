<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use PDO;

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
        //var_dump($array);
        $message = new MessageEntity(
            $array['content'],
            Locales::getLocalDateTime($array['sent_at']),
            Locales::getLocalDateTime($array['modified_at']),
            $this->memberRepository->findOneById($array['fk_from_member_id']),
            $this->memberRepository->findOneById($array['fk_to_member_id']),
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

    public function insert(MessageEntity $message): MessageEntity|false
    {
        $lastId = $this->dbManager->insert('messages', $message->toArray());
        if (is_int($lastId)) {
            $message->setId($lastId);
            return $message;
        }

        return false;
    }

    public function update(int $messageId, MessageEntity $message): bool
    {
        return $this->dbManager->update('messages', $messageId, $message->toArray());
    }

    public function delete(MessageEntity $message): bool
    {
        return $this->dbManager->delete('messages', $message->toArray());
    }

    public function deleteAll(): bool
    {
        return $this->dbManager->deleteAll('messages');
    }

    public function findOneById(int $id): MessageEntity|null
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

    public function findAll(): array
    {
        $messages = $this->arrayToMessage(
            $this->dbManager->findAll('messages')
        );

        return $messages;
    }

    public function findAllByRecipient(int|MemberEntity $recipient): array
    {
        if (is_int($recipient)) {
            $id = $recipient;
        } else {
            $id = $recipient->getId();
        }
        return $this->arrayToMessage(
            $this->dbManager->queryCustom(
                'SELECT *
            FROM messages
            WHERE fk_to_member_id = :member_id',
                [
                    'member_id' => [$id, PDO::PARAM_INT,],
                ]
            )
        );
    }

    public function findAllByMember(int|MemberEntity $member): array
    {
        if (is_int($member)) {
            $id = $member;
        } else {
            $id = $member->getId();
        }
        $results = $this->dbManager->queryCustom(
            "SELECT if(fk_from_member_id = :member_id , fk_to_member_id, fk_from_member_id) as user,
                    sent_at,
                    if(fk_from_member_id = :member_id , 'sent', 'received') as action,
                    content
            FROM messages
            WHERE fk_from_member_id = :member_id OR fk_to_member_id = :member_id
            ORDER BY user",
            [
                'member_id' => [$id, PDO::PARAM_INT,],
            ]
        );

        $mybox = [];
        foreach ($results as $message) {
            $id = $message['user'];
            $mybox[$id][] = $message;
        }

        return $mybox;
    }

    public function findAllBySender(int|MemberEntity $sender): array
    {
        if (is_int($sender)) {
            $id = $sender;
        } else {
            $id = $sender->getId();
        }
        return $this->arrayToMessage(
            $this->dbManager->queryCustom(
                'SELECT *
            FROM messages
            WHERE fk_from_member_id = :member_id',
                [
                    'member_id' => [$id, PDO::PARAM_INT,],
                ]
            )
        );
    }

    public function findAllByIsRead(MessageStatusEnum $status): array
    {
        return $this->arrayToMessage(
            $this->dbManager->queryCustom(
                'SELECT *
            FROM messages
            WHERE is_read = :status',
                [
                    'status' => [$status->value, PDO::PARAM_STR,],
                ]
            )
        );
    }
}
