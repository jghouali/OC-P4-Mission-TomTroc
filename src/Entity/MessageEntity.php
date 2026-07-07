<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use DateTime;
use Green\TomTroc\Core\Service\ValidatorService;
use Green\TomTroc\Enum\MessageStatusEnum;
use Green\TomTroc\Enum\ValidatorEnum;

class MessageEntity extends AbstractEntity
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

        $this->content = ValidatorService::validateField('content', $content, ValidatorEnum::textContent2000);
        $this->sentAt = ValidatorService::validateField('sentAt', $sentAt, ValidatorEnum::humanDate);
        $this->modifiedAt = ValidatorService::validateField('modifiedAt', $modifiedAt, ValidatorEnum::humanDate);
        $this->fromMember = $fromMember;
        $this->toMember = $toMember;
        $this->isRead = $isRead;
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'sent_at' => $this->sentAt->format('Y-m-d H:i:s'),
            'modified_at' => $this->modifiedAt->format('Y-m-d H:i:s'),
            'fk_from_member_id' => $this->fromMember->getId(),
            'fk_to_member_id' => $this->toMember->getId(),
            'is_read' => $this->isRead->value,
            $this->getStorageIdName() => $this->getId(),
        ];
    }

    public static function getStorageIdName(): string
    {
        return 'message_id';
    }

    public function setContent(string $content): void
    {
        $this->content = ValidatorService::validateField('content', $content, ValidatorEnum::textContent2000);
    }

    public function setIsRead(MessageStatusEnum $isRead): void
    {
        $this->isRead = $isRead;
    }

    public function setSentAt(DateTime $sentAt): void
    {
        $this->sentAt = ValidatorService::validateField('sentAt', $sentAt, ValidatorEnum::humanDate);
    }

    public function setModifiedAt(DateTime $modifiedAt): void
    {
        $this->modifiedAt = ValidatorService::validateField('modifiedAt', $modifiedAt, ValidatorEnum::humanDate);
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
