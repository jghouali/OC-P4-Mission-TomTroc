<?php

declare(strict_types=1);

namespace Green\TomTroc\Repository;

use Green\TomTroc\Core\Database\StorageInterface;
use Green\TomTroc\Core\Lib\Locales;
use Green\TomTroc\Entity\MemberEntity;
use Green\TomTroc\Entity\MessageEntity;
use Green\TomTroc\Enum\MessageStatusEnum;
use PDO;
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
        $messageId = $message->getId();
        if (is_int($messageId) && $messageId > 0) {
            throw new RuntimeException('This message already inserted', 400);
        }

        if ($message->getFromMember()->getId() === null) {
            throw new RuntimeException('FromMember Id is null', 400);
        }

        if ($message->getToMember()->getId() === null) {
            throw new RuntimeException('ToMember Id is null', 400);
        }

        $lastId = $this->dbManager->insert('messages', $message->toArray());
        if (is_int($lastId)) {
            $message->setId($lastId);
            return $message;
        }

        return false;
    }

    public function update(int $messageId, MessageEntity $message): MessageEntity|false
    {
        if ($message->getId() !== null && $message->getId() !== $messageId) {
            throw new RuntimeException('messageId mismatch with messageId whithin the given MemberEntity', 400);
        }

        if ($message->getFromMember()->getId() === null) {
            throw new RuntimeException('Null memberId in the given FromMember', 400);
        }

        if ($message->getToMember()->getId() === null) {
            throw new RuntimeException('Null memberId in the given ToMember', 400);
        }

        if (!$this->findOneById($messageId)) {
            throw new RuntimeException('messageId doesnt exist', 400);
        }
        $result = $this->dbManager->update('messages', $messageId, $message->toArray());

        if ($result === 1) {
            $message->setId($messageId);
            return $message;
        } elseif ($result === 0) {
            return false;
        }
        throw new RuntimeException('More than 1 entry updated', 500);
    }

    public function delete(MessageEntity $message): bool
    {
        if ($message->getId() === null) {
            throw new RuntimeException('messageId is null', 400);
        }

        if (!$this->findOneById($message->getId())) {
            throw new RuntimeException('messageId doesnt exist', 400);
        }

        $result = $this->dbManager->delete('messages', $message->toArray());

        if ($result === 1) {
            return true;
        } elseif ($result === 0) {
            return false;
        }
        throw new RuntimeException('More than 1 entry deleted', 500);
    }

    public function deleteAll(): bool
    {
        return $this->dbManager->deleteAll('messages');
    }

    public function findOneById(int $id): MessageEntity|null
    {
        $primary_key = MessageEntity::getStorageIdName();

        $result = $this->dbManager->queryCustom(
            "SELECT *
            FROM messages
            WHERE $primary_key = :message_id",
            [
                'message_id' => [$id, PDO::PARAM_INT,],
            ]
        );

        if (count($result) === 1) {
            return $this->oneToMessage($result[0]);
        }
        return null;
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

    public function findAllByMemberSorted(int|MemberEntity $member): array
    {
        if (is_int($member)) {
            $id = $member;
        } else {
            $id = $member->getId();
        }
        $results = $this->dbManager->queryCustom(
            'SELECT if(fk_from_member_id = :member_id , fk_to_member_id, fk_from_member_id) as user,
                    sent_at,
                    if(fk_from_member_id = :member_id , \'sent\', \'received\') as action,
                    content
            FROM messages
            WHERE fk_from_member_id = :member_id OR fk_to_member_id = :member_id
            ORDER BY user',
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

    public function findAllByMemberNotRead(int|MemberEntity $member): array
    {
        if (is_int($member)) {
            $id = $member;
        } else {
            $id = $member->getId();
        }
        $results = $this->dbManager->queryCustom(
            'SELECT *
            FROM messages
            WHERE fk_to_member_id = :member_id
            AND is_read = 0',
            [
                'member_id' => [$id, PDO::PARAM_INT,],
            ]
        );

        return $results;
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

    public function setReadtoAllMessageByUser(MemberEntity $toMember, MemberEntity $fromMember)
    {
        $result = $this->dbManager->queryCustom(
            'UPDATE messages
            SET is_read = 1
            WHERE fk_from_member_id = :fromMember
            AND fk_to_member_id = :toMember',
            [
                'fromMember' => [$fromMember->getId(), PDO::PARAM_INT,],
                'toMember' => [$toMember->getId(), PDO::PARAM_INT,],
            ]
        );
        if ($result === []) {
            return true;
        }
        return false;
    }
}
