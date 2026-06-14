<?php

declare(strict_types=1);

namespace Green\TomTroc\Entity;

use DateTime;
use Green\TomTroc\Enum\MessageStatusEnum;
use RuntimeException;

class MessageEntity
{
    private string $content;
    private MessageStatusEnum $isRead;
    private DateTime $sentAt;
    private DateTime $modifiedAt;
    private MemberEntity $fromMember;
    private MemberEntity $toMember;

    public function __construct(
        string $content,
        DateTime $sentAt,
        DateTime $modifiedAt,
        MemberEntity $fromMember,
        MemberEntity $toMember,
        MessageStatusEnum $isRead,
    ) {

        $this->content = $this->validateField('content', $content);
        $this->isRead = $this->validateField('isRead', $isRead);
        $this->sentAt = $this->validateField('sentAt', $sentAt);
        $this->modifiedAt = $this->validateField('modifiedAt', $modifiedAt);
        $this->fromMember = $this->validateField('fromMember', $fromMember);
        $this->toMember = $this->validateField('toMember', $toMember);
    }

    private function validateField(string $property, mixed $field): mixed
    {
        switch ($property) {
            case 'content':
                // A-Z or a-z or 0-9 or _ or - minimum 1 maximum 50
                $validated = filter_var(
                    $field,
                    FILTER_VALIDATE_REGEXP,
                    ['options' => ['regexp' => '/^[a-zA-Z0-9_-]{1,50}$/']]
                );
                $message = 'Content must only contain character in a-z, A-Z, 0-9, _ or -';
                break;

            case 'isRead':
                // it is an Enum
                $validated = true;
                break;

            case 'sentAt':
                // must be a date time before now and not before 110 years ago
                $validated = (
                    $field <= new DateTime('now') &&
                    $field > new DateTime('110 years ago')
                );
                $message = 'Sent Date must be before now and afer 110 years ago';
                break;

            case 'modifiedAt':
                // must be a date time before now and not before 110 years ago
                $validated = (
                    $field <= new DateTime('now') &&
                    $field > new DateTime('110 years ago')
                );
                $message = 'Modified Date must be before now and afer 110 years ago';
                break;

            case 'fromMember':
                // must be an instance of MemberEntity
                $validated = (get_class($field) === 'Green\TomTroc\Entity\MemberEntity');
                $message = 'fromMember must be a MemberEntity';
                break;

            case 'toMember':
                // must be an instance of MemberEntity
                $validated = (get_class($field) === 'Green\TomTroc\Entity\MemberEntity');
                $message = 'toMember must be a MemberEntity';
                break;

            default:
                throw new RuntimeException('Unknow field passed to the validator');
        }

        if ($validated) {
            return $field;
        } else {
            $message = !isset($message) ? 'Unknown error' : $message;
            throw new RuntimeException("Invalid $property : $message");
        }
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getIsRead(): MessageStatusEnum
    {
        return $this->isRead;
    }

    public function getSentAt(): DateTime
    {
        return $this->sentAt;
    }

    public function getModifiedAt(): DateTime
    {
        return $this->modifiedAt;
    }

    public function getFromMember(): MemberEntity
    {
        return $this->fromMember;
    }

    public function getToMember(): MemberEntity
    {
        return $this->toMember;
    }

    public function setContent(string $content): void
    {
        $this->content = $this->validateField('content', $content);
    }

    public function setIsRead(MessageStatusEnum $isRead): void
    {
        $this->isRead = $this->validateField('isRead', $isRead);
    }

    public function setSentAt(DateTime $sentAt): void
    {
        $this->sentAt = $this->validateField('sentAt', $sentAt);
    }

    public function setModifiedAt(DateTime $modifiedAt): void
    {
        $this->modifiedAt = $this->validateField('modifiedAt', $modifiedAt);
    }

    public function setFromMember(MemberEntity $fromMember): void
    {
        $this->fromMember = $this->validateField('fromMember', $fromMember);
    }

    public function setToMember(MemberEntity $toMember): void
    {
        $this->toMember = $this->validateField('toMember', $toMember);
    }
}
