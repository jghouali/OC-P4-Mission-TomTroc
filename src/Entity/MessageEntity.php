<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use DateTime;
use Green\TomTroc\Enum\MessageStatusEnum;
use Green\TomTroc\Enum\ValidatorEnum;

class MessageEntity extends AbstractEntity implements EntityInterface
{
    protected string $content;
    protected DateTime $sentAt;
    protected DateTime $modifiedAt;
    protected MemberEntity $fromMember;
    protected MemberEntity $toMember;
    protected MessageStatusEnum $isRead;

    public function __construct(
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {

        $this->content = $this->validateField('content', $content, ValidatorEnum::alphanumeric_150);
        $this->sentAt = $this->validateField('sentAt', $sentAt, ValidatorEnum::humanDate);
        $this->modifiedAt = $this->validateField('modifiedAt', $modifiedAt, ValidatorEnum::humanDate);
        $this->fromMember = $fromMember;
        $this->toMember = $toMember;
        $this->isRead = $isRead;
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'sentAt' => $this->sentAt->format('Y-m-d H:i:s'),
            'modifiedAt' => $this->modifiedAt->format('Y-m-d H:i:s'),
            'fkFromMemberId' => $this->fromMember->getId(),
            'fkToMemberId' => $this->toMember->getId(),
            'isRead' => $this->isRead->value,
            $this->getStorageIdName() => $this->getId(),
        ];
    }

    public static function getStorageIdName(): string
    {
        return 'message_id';
    }

    public function setContent(string $content): void
    {
        $this->content = $this->validateField('content', $content, ValidatorEnum::alphanumeric_150);
    }

    public function setIsRead(MessageStatusEnum $isRead): void
    {
        $this->isRead = $isRead;
    }

    public function setSentAt(DateTime $sentAt): void
    {
        $this->sentAt = $this->validateField('sentAt', $sentAt, ValidatorEnum::humanDate);
    }

    public function setModifiedAt(DateTime $modifiedAt): void
    {
        $this->modifiedAt = $this->validateField('modifiedAt', $modifiedAt, ValidatorEnum::humanDate);
    }

    public function setFromMember(MemberEntity $fromMember): void
    {
        $this->fromMember = $fromMember;
    }

    public function setToMember(MemberEntity $toMember): void
    {
        $this->toMember = $toMember;
    }
}
